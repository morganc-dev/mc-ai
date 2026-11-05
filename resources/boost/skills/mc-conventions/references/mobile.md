# Native Shell Conventions

Load only when the project ships a native shell such as Capacitor and the task touches its specifics. The shell is never a package dependency or an assumption.

- Do not assume a browser API behaves identically in the browser, on Android, and on iOS; check the shell's plugin or the project's abstraction before relying on it.
- Use the project's existing abstractions for native features; do not call a plugin directly where a wrapper exists.
- Never place a secret in the frontend bundle or the shell configuration; the backend holds it.
- State when a change needs validation on a device or emulator and whether that validation was performed.
