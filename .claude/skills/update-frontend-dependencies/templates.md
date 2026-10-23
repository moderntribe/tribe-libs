# Templates

## Phase 1 Approval Package

```markdown
## Dependency Upgrade Plan

### Project Snapshot
- Package manager:
- Node signals found:
- Primary frontend build command:

### Removal Candidates
- `package-name` — confidence: high|medium|low — why:

### Node Recommendation
- Current:
- Recommended target:
- Why:
- Files/workflows impacted:

### Proposed Upgrade Sequence
1. Runtime dependencies
- `package-name` -> `target-version` — risk: low|medium|high — notes:
- Complete low-risk patch and minor updates before major-version migrations.

2. Dev dependencies
- `package-name` -> `target-version` — risk: low|medium|high — notes:
- Complete low-risk patch and minor updates before major-version migrations.

### Major Version And Migration Risks
- `package-name` — breaking changes:

### Deprecated Or Unmaintained Packages
- `package-name` — status:
- Recommendation:
- Alternatives:

### Validation Plan
- Build:
- Browser review targets and available tooling:
- Extra checks:

### Open Questions / Approval Needed
- Question or decision:
```

## Final Report

```markdown
## Dependency Update Report

### Node
- Changed:
- Details:

### Removed
- `package-name` — why:

### Updated Runtime Dependencies
- `package-name` -> `version` — code/config changes:

### Updated Dev Dependencies
- `package-name` -> `version` — code/config changes:

### Validation Performed
- Build results:
- Browser checks:
- Console/error review:

### Highest Regression Risks
- Risk:

### Follow-Up Manual QA
- Area to verify:
```
