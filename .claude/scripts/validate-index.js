#!/usr/bin/env node
"use strict";

const fs = require("fs");

function fail(message) {
  console.error(`Error: ${message}`);
  process.exit(1);
}

const path = "skills.json";
if (!fs.existsSync(path)) {
  fail("skills.json does not exist");
}

const index = JSON.parse(fs.readFileSync(path, "utf8"));
if (!("version" in index)) {
  fail("skills.json missing version field");
}
if (!("skills" in index)) {
  fail("skills.json missing skills array");
}
if (!Array.isArray(index.skills)) {
  fail("skills must be an array");
}

const ids = index.skills.map((s) => s.id);
if (ids.length !== new Set(ids).size) {
  fail("duplicate skill IDs found");
}

const semver = /^\d+\.\d+\.\d+$/;
for (const skill of index.skills) {
  if (!semver.test(skill.version || "")) {
    fail(`invalid version in ${skill.id}: ${skill.version}`);
  }
}

console.log(`Validated ${index.skills.length} skills`);
