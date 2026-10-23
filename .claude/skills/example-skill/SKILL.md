---
name: example-skill
description: A template showing how to structure a tribe skill with metadata, labels, and clear documentation.
---

# Example Skill

This is an example skill demonstrating the structure and best practices for creating tribe skills.

## Purpose

This skill serves as a template for creating new skills in the tribe-skills repository.

## When to Use

Use this skill as a reference when:
- Creating a new skill
- Understanding skill structure
- Learning skill documentation patterns

## Workflow

1. **Define the goal**: What problem does this skill solve?
2. **Outline the steps**: Break down the process into clear, actionable steps
3. **Document triggers**: When should this skill be used?
4. **Add examples**: Show concrete usage examples

## Structure Guidelines

### Frontmatter

Skills should include YAML frontmatter with:
- `name`: The skill identifier (matches skill.yaml id)
- `description`: Brief description of purpose

### Sections

Recommended sections:
- **Purpose**: What does this skill do?
- **When to Use**: Clear trigger conditions
- **Workflow**: Step-by-step process
- **Examples**: Concrete usage examples
- **Troubleshooting**: Common issues and solutions

## Example Workflow

1. Read the skill file to understand the task
2. Gather required context
3. Execute the defined steps
4. Verify outcomes
5. Report results to the user

## Metadata

Each skill should have a corresponding `skill.yaml` file with:

```yaml
id: example-skill
name: Example Skill
description: Brief description
version: 1.0.0
labels:
  - category1
  - category2
```

## Best Practices

- Keep skills focused on a single purpose
- Use clear, actionable language
- Include concrete examples
- Document dependencies
- Version skills semantically
- Add searchable labels

## Success Criteria

- Clear trigger conditions
- Step-by-step instructions
- Concrete examples
- Proper metadata
- Useful labels for discovery
