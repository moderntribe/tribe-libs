# Tribe Skills

A curated repository of AI agent skills for team workflows. Install, update, and manage skills across your team's Cursor environments.

## Quick Start

```bash
# Install global CLI wrapper (recommended)
./scripts/install-cli.sh

# List available skills
tribe-skills list

# Install a skill
tribe-skills install figma-design-sync

# Uninstall a skill
tribe-skills uninstall figma-design-sync

# Update all installed skills
tribe-skills update --all

# Check for outdated skills
tribe-skills outdated

# Verify installation health
tribe-skills doctor
```

## Installation

Skills are installed to `~/.cursor/skills` by default. Override with `TRIBE_SKILLS_HOME` environment variable.

### Install the CLI globally

Run once from the repository root:

```bash
./scripts/install-cli.sh
```

This creates `~/.local/bin/tribe-skills`, so you can run commands from any directory.

If needed, add this to your shell profile:

```bash
export PATH="$HOME/.local/bin:$PATH"
```

To uninstall the global wrapper:

```bash
./scripts/uninstall-cli.sh
```

### Requirements

- Bash 4.0+
- Node.js 18+
- Cursor IDE

## Available Skills

Run `tribe-skills list` to see all available skills with descriptions and labels.

## Usage

### List Skills

```bash
# List all skills
tribe-skills list

# Filter by label
tribe-skills list --label jira
tribe-skills list --label workflow
```

### Install Skills

```bash
# Install a specific skill
tribe-skills install figma-design-sync

# Skills are copied to ~/.cursor/skills/<skill-id>/
```

### Uninstall Skills

```bash
# Uninstall a specific skill
tribe-skills uninstall figma-design-sync
```

### Update Skills

```bash
# Update a specific skill
tribe-skills update figma-design-sync

# Update all installed skills
tribe-skills update --all
```

### Check Status

```bash
# List outdated skills
tribe-skills outdated

# Verify installation integrity
tribe-skills doctor
```

## Contributing

### Adding a New Skill

1. Create skill directory: `skills/<skill-id>/`
2. Add `SKILL.md` with skill content
3. Add `skill.yaml` with metadata:

```yaml
id: my-skill
name: My Skill
description: What this skill does
version: 1.0.0
labels:
  - category1
  - category2
```

4. Run `node scripts/build-index.js` to regenerate `skills.json`
5. Commit all files and open a PR

### Skill Metadata Schema

See [schema/skill.schema.json](schema/skill.schema.json) for the complete metadata specification.

### Version Convention

- Use semantic versioning: `MAJOR.MINOR.PATCH`
- Increment MAJOR for breaking changes
- Increment MINOR for new features
- Increment PATCH for bug fixes

## Repository Structure

```
tribe-skills/
├── cli/
│   └── tribe-skills          # Main CLI script
├── skills/
│   ├── figma-design-sync/    # Skill directory
│   │   ├── SKILL.md          # Skill content
│   │   └── skill.yaml        # Skill metadata
│   └── .../
├── scripts/
│   ├── build-index.js        # Index generation script
│   ├── validate-index.js     # Index validation script
│   ├── install-cli.sh        # Global CLI installer
│   └── uninstall-cli.sh      # Global CLI uninstaller
├── schema/
│   └── skill.schema.json     # Metadata schema
├── .github/
│   └── workflows/
│       └── validate.yml      # CI validation
└── skills.json               # Generated index
```

## CI Validation

The repository includes GitHub Actions workflow that validates:

- All skill directories have required files (SKILL.md, skill.yaml)
- Metadata conforms to schema
- skills.json is up-to-date with skill metadata
- No duplicate skill IDs
- Valid semantic versions

## License

Proprietary - Tribe internal use only.
