+++
title = "Roadmap"
description = "Where Fly stands and the ordered plan to maturity — a truthful compiler, a sound bootstrap, a safe language, expressive power, a coherent standard library and the criteria for 1.0 — tracked phase by phase in milestones and issues on GitHub."
template = "docs-page.html"
weight = 8
+++

# Roadmap to maturity

Fly compiles itself. The compiler, the runtime and the standard library are
written in Fly, and the toolchain ships a language server, a package registry
and a debugger. What separates the current release from a language people can
depend on is not a list of missing features — it is **trust**: that the compiler
enforces what the specification says, that the released binaries are built
entirely by Fly, and that a program which compiles cannot fail silently.

Four principles decide every item below and settle conflicts in this order:

1. **Simplicity** — one way to do a thing; no rule that surprises.
2. **Reliability** — an error is never ignored by default.
3. **Security** — unsafe operations are visible, bounded and opt-in.
4. **Power** — added only once the first three hold.

The current status of every rule mentioned here is recorded in
[Conformance](@/docs/Fly-Conformance.md).

## Where to follow it

The plan is tracked on GitHub, in the [fly-lang/fly](https://github.com/fly-lang/fly)
repository: one **milestone per phase**, one **epic issue** per phase with the
checklist of its issues, and one issue per piece of work — done or to do. A
closed issue carries the `release:*` label of the first release that contains
it; the `area:*` labels say which part of the toolchain it touches.

| Phase | Status | Milestone | Epic |
|---|---|---|---|
| 0 — Truth | done (v0.14.9) | [Phase 0](https://github.com/fly-lang/fly/milestone/9?closed=1) | [#229](https://github.com/fly-lang/fly/issues/229) |
| 1 — Sound bootstrap | done (v0.14.9 – v0.15.2) | [Phase 1](https://github.com/fly-lang/fly/milestone/10?closed=1) | [#230](https://github.com/fly-lang/fly/issues/230) |
| 2 — Safe language | in progress, closes with 0.16.0 | [Phase 2](https://github.com/fly-lang/fly/milestone/11) | [#231](https://github.com/fly-lang/fly/issues/231) |
| 2b — Power | planned | [Phase 2b](https://github.com/fly-lang/fly/milestone/12) | [#232](https://github.com/fly-lang/fly/issues/232) |
| 3 — Coherent std | planned, alongside phase 2 | [Phase 3](https://github.com/fly-lang/fly/milestone/13) | [#233](https://github.com/fly-lang/fly/issues/233) |
| 4 — Ecosystem and 1.0 | planned | [Phase 4](https://github.com/fly-lang/fly/milestone/14) | [#234](https://github.com/fly-lang/fly/issues/234) |

Releases: [v0.13.19](https://github.com/fly-lang/fly/releases/tag/v0.13.19) (the
last C++ seed), [v0.14.9](https://github.com/fly-lang/fly/releases/tag/v0.14.9),
[v0.15.0](https://github.com/fly-lang/fly/releases/tag/v0.15.0),
[v0.15.2](https://github.com/fly-lang/fly/releases/tag/v0.15.2); next, 0.16.0.

---

## The chain of seeds

Fly has two compilers in its history: the C++ **seed**, frozen at v0.13.19, and
the **self-host** written in Fly — see [Building Fly](@/docs/Fly-Bootstrap.md).
The end state the plan set out to reach is the one Rust and Go reached, and it
is reached: **the seed of a release line is the previous self-host release**.
v0.13.19 built the 0.14 line, v0.14.9 built the 0.15 line, v0.15.2 builds the
0.16 line. Each line lives on its own `release/<version>.X` branch, so the whole
chain can be rebuilt in order from the C++ compiler. A change that needs a
different seed opens the next line; every new feature, check and library goes
into the self-host only.

---

## Phase 0 — Truth

*The compiler does what the documentation says.* **Done — shipped in v0.14.9.**

- **Semantic parity**: the self-host rejects what the C++ seed rejected —
  implicit narrowing, `private` access, instantiating an `abstract` class,
  overriding a `final` method, `break` outside a loop, redeclarations,
  `string + int` — and also enforces `const`, `private` methods and unique
  signatures.
- **A blocking semantic guard** (`test_sema`): every rule has a probe, judged by
  both compilers on every build.
- **No internal errors, no silent options**: a non-`bool` condition or code after
  a `return` gets a diagnostic; an option with no effect says so.
- **The CI tells the truth**: the seed is pinned in one place and verified by
  SHA-256 before use, a failed stage 2 is fatal, releases publish `SHA256SUMS`.
- **Documentation** realigned with the compiler.

## Phase 1 — A sound bootstrap

*Everything that ships is built by Fly, and the build proves it.* **Done —
shipped in v0.14.9, v0.15.0 and v0.15.2.**

- One class layout and interface dispatch through per-class tables, so the
  standard library and the runtime that ship are compiled by the self-host —
  no object file comes from the seed.
- **Stage 3** on every build: the released compiler recompiles itself and the
  object is compared byte for byte, on Linux x86_64, Windows x64 and macOS arm64.
- **Reproducible builds**, checked in CI: two clean builds give the same bytes.
- **Debug information** from the self-host — lines, parameters, locals, types,
  class fields — so the bundled `lldb` resolves source lines.
- A bootstrap without privileges, the LLVM archives verified by SHA-256, and
  **macOS arm64** as a supported platform.
- The seed becomes the previous self-host release.

## Phase 2 — A safe language

*A program that compiles does not fail silently.* **In progress — closes with
0.16.0.**

Done:

- **Errors cannot be ignored**: a failure in a called function stops the caller
  too, up to a `handle` or `main` — always, with no switch and no annotation.
- **Diagnosed run-time faults**, in every build: a null reference, a division by
  zero and an integer overflow stop the program with a message and a source
  position; an index out of range fails with its position; an access through a
  raw pointer that points nowhere and a stack overflow print what happened
  before the system ends the program.
- **Deterministic release**: a plain `new` requires `delete`; automatic release
  belongs to `new unique` (one owner) and `new shared` (reference counted), with
  `weak` references to break cycles.
- **Warnings** (`unused-variable`, `unused-import`, `shadow`, `lossy-conversion`,
  `non-exhaustive-switch`) with `--warnings-as-errors`; the tree builds with it.
- **Conversions**: narrowing and signed-to-unsigned need a cast, also for
  arguments; mixed signed/unsigned comparisons are errors.
- **No `switch` fall-through**; **unambiguous precedence** — mixing `&&` with
  `||`, or bitwise with comparison operators, needs parentheses.
- **Strings**: escapes in ordinary literals, raw `"""…"""` literals, a UTF-8
  character API; no `char` type.
- **Privileged namespaces** instead of an `unsafe` keyword: `fly.llvm` and
  `fly.runtime` are reserved to the standard library and the toolchain; user
  code reaches raw memory through `fly.mem`.
- **Fly builds itself with `fly build`**: the version lives in `Manifest.fly`,
  every program can read its own manifest (`fly.meta.package`), and all three
  bootstrap stages are `fly build` on the repository's own manifest — the
  result is a self-contained toolchain.
- **`protected` is checked**: a protected member is reachable from its class and
  every class derived from it, and is written only on class methods and
  attributes; `private` and `protected` also guard static members.

To do: no implicit conversion to `bool`, constant overflow as a
compile-time error, and the remaining clean-ups — see the
[milestone](https://github.com/fly-lang/fly/milestone/11).

**Exit:** no undefined or unchecked behaviour is reachable from user code except
through `fly.mem`.

## Phase 2b — Power

*Expressive power, once safety holds.* **Planned.** Every item the compiler's
own sources want to use goes through a new seed release line.

1. **Functions as values**: function types, lambdas and closures, with capture
   rules tied to the memory model — the prerequisite for sorting with a
   comparator, callbacks and threads.
2. **Complete generics**: generic methods, explicit type arguments
   `f<int>(x)`, constraints (`T : Comparable`), generic interfaces, `this.m()`
   inside templates.
3. **Algebraic data types and `match`**: enums with data, exhaustive `match`;
   `Option<T>` and `Result<T, E>` next to `fail`.
4. **Iteration**: `Iterable`/`Iterator` for `for x in coll` over user types;
   ranges `0..n`.
5. **Ergonomics**: string interpolation, destructuring of multiple returns,
   compile-time constants.
6. **Concurrency**: threads and channels (or async) in the standard library,
   designed around `shared`.
7. **Modules and packages**: module and package visibility, versioned
   dependencies through `fly-registry`.
8. **Module names in lower case**, so no two modules differ only by case.

Left out on purpose, for simplicity: operator overloading, reflection, multiple
inheritance, macros.

## Phase 3 — A coherent standard library

*One model, applied everywhere.* **Planned, alongside phase 2.** The library is
already broad — sockets, TLS on two back ends, HTTP, JSON, gzip and tar in pure
Fly, SHA-256. What it lacks is uniformity.

1. **One error model**: every module fails with `fail` and documented codes; no
   sentinel values, `errOut` parameters or silent defaults.
2. **Typed handles**: `Socket`, `TlsConn`, `JsonValue`, `PollSet`, `Process`
   instead of opaque `pointer` and `long` values.
3. **Collections**: iteration over every container, sorting, searching,
   `keys()`/`values()`; a balanced `Tree`; seeded hashing.
4. **Text**: `split`, `join`, formatting, number parsing, a string builder.
5. **Networking**: IPv6 and `getaddrinfo`; typed HTTP with size limits; one
   client that picks TLS from the URL scheme; a server loop.
6. **Security**: a cryptographic random source, HMAC, SHA-512, constant-time
   comparison; depth and expansion limits in the decoders.
7. **Concurrency**: `join`, condition variables, public atomics, channels.
8. **Processes and time**: pipes and in-memory capture, child environment and
   time-outs; time zones.
9. **New modules**: formatting, regular expressions, argument parsing,
   cryptography, UUID, CSV.
10. **Quality**: fuzzing of every parser, reference benchmarks, a check that
    every public function is documented.

**Exit:** no public function reports failure by a sentinel or a silent default.

## Phase 4 — Ecosystem and 1.0

**Planned.**

- A documentation generator and test coverage (the formatter, `fly fmt`, already
  ships); parallel builds in `fly build`.
- Signed packages and audited dependencies in the registry.
- A normative **specification with a conformance suite** as the source of truth.
- A stability policy: semantic versioning, editions for incompatible changes,
  deprecation with one release of notice.
- More platforms, starting with Linux aarch64, through the same chain.

### Criteria for 1.0

- The stage 3 fixpoint holds on every supported target.
- The specification, the conformance suite and the compiler agree, with no
  undocumented divergence.
- No unchecked behaviour is reachable from user code except through `fly.mem`.
- The standard library uses a single error model.
- The documentation is generated from, and verified against, the sources.
