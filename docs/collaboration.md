# Collaboration workflow

## Working agreement

The project is being prepared for two students. The role allocation below is proposed; the partner account, collaborator invitation, and final ownership have not yet been configured or confirmed.

Both students should understand the request flow, run the project locally, and review each other's work. Each needs a separate ignored connection configuration. A public repository allows reading but does not automatically grant permission to push branches.

## Proposed ownership

| Task | Primary owner | Reviewer |
| --- | --- | --- |
| SNMP access and System page | Student A | Student B |
| TCP statistics and interface consistency | Student B | Student A |
| Certificate Batch script | Student B | Student A |
| Encryption Batch script | Student A | Student B |
| Local certificate generation and exchange | Each student | Both |
| Documentation and final verification | Both | Both |

The SNMP and OpenSSL parts are independent and can proceed in parallel after agreeing on file locations. Their integration is not required by the handout.

## Branch names

Use descriptive names with a conventional purpose prefix:

```text
feature/system-group
feature/tcp-statistics
feature/certificate-script
feature/file-encryption
fix/snmp-timeout-feedback
docs/project-guide-and-learning-notes
```

Project branches should not use the `codex/` prefix.

## Small-task workflow

1. Update clean local `main` using `git pull --ff-only`.
2. Create a branch for one focused task, such as `git switch -c feature/system-group`.
3. Implement the behavior, verify it, and update its documentation.
4. Stage explicit files and inspect `git diff --cached`.
5. Commit a meaningful message and push using `git push -u origin <branch-name>`.
6. Open a pull request describing the change and its verification.
7. Have the other student review it before merging agreed changes into `main`.

Do not commit communities, private keys, generated outputs, or machine-specific IDE settings. Resolve conflicts by understanding both changes rather than discarding the partner's work.

## Review checklist

- Does the change satisfy a requirement or a documented supporting task?
- Can the author explain its implementation and verification?
- Are failures handled without revealing credentials?
- Can the second workstation reproduce the setup?
- Is relevant documentation updated?
- Are personal and generated files excluded?

## Certificates and submission

Each student generates their own key pair and self-signed certificate. Exchange certificates through the agreed channel; keep private keys private. PEM files are excluded from Git by default.

The handout requires each partner to submit separately. A video is conditional on the teaching assistant's instructions; no upload has been prepared or performed.
