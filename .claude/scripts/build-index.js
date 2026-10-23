#!/usr/bin/env node
"use strict";

const fs = require("fs");
const path = require("path");

function parseYamlSubset(content) {
  const data = {};
  let currentListKey = null;

  for (const rawLine of content.split(/\r?\n/)) {
    const line = rawLine.trimEnd();
    if (!line || line.trimStart().startsWith("#")) continue;

    if (line.startsWith("  - ")) {
      if (currentListKey) data[currentListKey].push(line.slice(4).trim());
      continue;
    }

    if (line.startsWith("  ")) continue;

    const sep = line.indexOf(":");
    if (sep === -1) continue;

    const key = line.slice(0, sep).trim();
    const value = line.slice(sep + 1).trim();

    if (value) {
      data[key] = value;
      currentListKey = null;
    } else {
      data[key] = [];
      currentListKey = key;
    }
  }

  return data;
}

const VALID_TARGETS = ["cursor", "claude"];

function validateSkill(skill, skillPath, expectedId) {
  const required = ["id", "name", "description", "version", "labels"];
  const missing = required.filter((field) => !(field in skill));
  if (missing.length > 0) {
    throw new Error(`Missing required fields (${missing.join(", ")}) in ${skillPath}`);
  }

  if (!Array.isArray(skill.labels) || skill.labels.length === 0) {
    throw new Error(`'labels' must be a non-empty list in ${skillPath}`);
  }

  if (!/^\d+\.\d+\.\d+$/.test(skill.version)) {
    throw new Error(`Invalid semver '${skill.version}' in ${skillPath}`);
  }

  if (skill.id !== expectedId) {
    throw new Error(`Skill id '${skill.id}' must match directory '${expectedId}' in ${skillPath}`);
  }

  if ("targets" in skill) {
    if (!Array.isArray(skill.targets) || skill.targets.length === 0) {
      throw new Error(`'targets' must be a non-empty list in ${skillPath}`);
    }
    const invalid = skill.targets.filter((t) => !VALID_TARGETS.includes(t));
    if (invalid.length > 0) {
      throw new Error(
        `Invalid targets (${invalid.join(", ")}) in ${skillPath}. Valid values: ${VALID_TARGETS.join(", ")}`
      );
    }
  }
}

function buildIndex() {
  const repoRoot = path.resolve(__dirname, "..");
  const skillsDir = path.join(repoRoot, "skills");
  const outputPath = path.join(repoRoot, "skills.json");

  if (!fs.existsSync(skillsDir)) {
    throw new Error(`skills directory not found: ${skillsDir}`);
  }

  const entries = fs
    .readdirSync(skillsDir, { withFileTypes: true })
    .filter((d) => d.isDirectory() && !d.name.startsWith("."))
    .map((d) => d.name)
    .sort();

  const skills = [];
  const seenIds = new Set();

  for (const skillId of entries) {
    const skillDir = path.join(skillsDir, skillId);
    const skillYaml = path.join(skillDir, "skill.yaml");
    const skillMd = path.join(skillDir, "SKILL.md");

    if (!fs.existsSync(skillYaml)) {
      console.warn(`Warning: ${skillId} missing skill.yaml, skipping`);
      continue;
    }
    if (!fs.existsSync(skillMd)) {
      console.warn(`Warning: ${skillId} missing SKILL.md, skipping`);
      continue;
    }

    const parsed = parseYamlSubset(fs.readFileSync(skillYaml, "utf8"));
    validateSkill(parsed, skillYaml, skillId);

    if (seenIds.has(parsed.id)) {
      throw new Error(`Duplicate skill id '${parsed.id}'`);
    }
    seenIds.add(parsed.id);
    skills.push(parsed);
  }

  const index = {
    version: "1.0",
    skills,
  };

  fs.writeFileSync(outputPath, `${JSON.stringify(index, null, 2)}\n`, "utf8");
  console.log(`Built index with ${skills.length} skills: ${outputPath}`);
}

try {
  buildIndex();
} catch (error) {
  console.error(`Error: ${error.message}`);
  process.exit(1);
}
