+++
title = "Roadmap"
description = "Where Fly stands after self-hosting and the ordered plan to maturity: a truthful compiler, a complete bootstrap, a safe language, a coherent standard library, and the criteria for 1.0."
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

---

## Seed or self-host?

Fly has two compilers: the C++ **seed** (0.13.x) and the **self-host** (0.14.x)
— see [Building Fly](@/docs/Fly-Bootstrap.md). Every change must choose one.
The rule:

> The seed is frozen at what is indispensable to compile the 0.14.x tree. It is
> changed only for a miscompile or crash that affects the tree, for an ABI or
> file-format constraint the shipped artifacts inherit from it, or for syntax
> the tree needs. **Every new feature, check, warning and library goes into the
> self-host only.**

The seed may accept more than the self-host — it only ever compiles code that
has already been validated — but never less. The end state is the one Rust and
Go reached: the seed of version *N* is the released self-host of version *N−1*,
and the C++ compiler becomes the historical root of the chain rather than a
dependency of every build.

---

## Phase 0 — Truth

*The compiler does what the documentation says.*

> **Status: done in 0.14.7.** The self-hosted compiler now enforces every rule the seed enforces, plus `const` on locals, parameters and fields, `private` on methods, and unique signatures within a file. Every one of them has a probe in the blocking `test_sema` guard, which also runs each probe through the seed. A non-`bool` condition and code after a `return` get a diagnostic instead of an internal error, and a semantic error is reported in its own file. Options with no effect say so. The seed archive is checked against a pinned SHA-256 before use, and a release publishes `SHA256SUMS`. Carried into phase 1: verifying the LLVM archive the same way, and running the stage 3 fixpoint check on every build rather than only on releases.

- **Semantic parity.** The self-host accepts programs the seed rejects:
  implicit narrowing, access to `private` members, instantiating an `abstract`
  class, overriding a `final` method, `break` outside a loop, redeclaring a
  variable, `string + int`. Each becomes a compile error again.
- **`const` is enforced** for locals, parameters and fields; two functions with
  an identical signature are an error.
- **No internal errors**: unreachable code after `return` gets a diagnostic.
- **A blocking differential suite**: one corpus of valid and invalid programs,
  one expected verdict, run against both compilers on every build.
- **No silent options**: a flag that is accepted is implemented.
- **Verified downloads**: the seed and LLVM archives are pinned by SHA-256;
  releases publish checksums.

**Exit:** the conformance table has no "not enforced" row that the seed
enforces.

## Phase 1 — A complete bootstrap

*Everything that ships is built by Fly, and the build proves it.*

- The self-host adopts the reference **class layout**, so the standard library
  is compiled by the self-host instead of being carried over from the seed.
- The runtime's **C primitives** are built from sources in the tree, or ported
  to Fly; no object file is inherited from the seed.
- **Stage 3**: the released compiler recompiles itself and the result is
  compared bit for bit — the fixpoint is demonstrated on every build. *(Runs on release builds since 0.14.7.)*
- **Reproducible builds**: two clean builds produce identical artifacts. *(Verified by hand on Linux and Windows on the 0.14.x branch; not yet checked automatically in CI.)*
- **Debug information** from the self-host, so the bundled `lldb` resolves
  source lines. *(Lines, parameters and locals from 0.14.8; class fields still to come.)*
- Bootstrap without elevated privileges. *(Done in 0.14.8.)*
- **macOS arm64**: built and tested through stage 3 in CI, shipped from 0.14.9.
  *(Done: seed 0.13.19 carries it.)*
- **Linux aarch64** (ARM 64-bit Linux): a later platform, through the same chain
  (LLVM, then a seed, then a self-host release).
- The seed becomes the previous self-host release. *(Done: `release/0.15.X` is
  seeded by 0.14.9.)*

**Exit:** `stage2 == stage3` on the three supported targets (Linux x86_64,
Windows x64, macOS arm64); the release contains no artifact compiled by the C++
seed.

## Phase 2 — A safe language

*A program that compiles does not fail silently.* All of this lands in the
self-host only.

1. **Errors cannot be ignored.** Today a `fail` in a called function lets the
   caller continue with a zero value. The call will have to be handled or
   propagated; propagation becomes the default and `handle` is what stops it.
   This is the most consequential change on the roadmap and will be introduced
   behind an edition switch.
2. **Warnings.** Unused variables and imports, shadowing, unreachable code,
   lossy conversions, non-exhaustive `switch` over an enum. *(In 0.15: the
   warning machinery — `--warnings-as-errors`, `--no-warn=<name>` — with
   `unused-variable` and `shadow`; code after a `return` is already an error.
   Still to come: unused imports, lossy conversions, non-exhaustive `switch`.)*
3. **Conversions.** Widening is implicit; narrowing needs an explicit cast.
   *(Done in 0.15: also for call arguments, signed into unsigned, and
   signed/unsigned comparisons.)*
4. **Null safety.** No new syntax: any class reference may be null, and every
   use of a null one stops the program with a diagnostic, in every build. *(Done
   in 0.15: the null trap.)*
5. **Diagnosed traps.** Division by zero and a null dereference stop the
   program with a message and a source position, not a bare crash; so does
   integer overflow. *(Done in 0.15, in every build: null reference, division by
   zero and integer overflow; wrapping is explicit with `math.wrapAdd` and its
   siblings.)*
6. **`switch` without implicit fall-through.** *(Done in 0.15: `case 3, 4:` lists
   several values, an empty case is an error.)*
7. **Deterministic release of resources**, replacing the "remember to call
   `free()`" convention. *(In 0.15: `delete`, `new unique`, `new shared` and
   `weak h`; a plain `new` will require `delete` from the next release branch.)*
8. **Operator precedence**: mixing bitwise, shift and comparison operators — or
   `&&` with `||` — without parentheses becomes an error instead of a surprise.
   *(Done in 0.15.)*
9. **Strings**: escape sequences in ordinary literals, explicit raw literals,
   and a UTF-8 model with distinct byte and code-point APIs.
10. **Generics**: generic methods, explicit type arguments, and internal calls
    inside generic classes.
11. **Privileged namespaces** instead of an `unsafe` keyword: `fly.llvm` and
    `fly.runtime` are callable only from the standard library and the toolchain;
    user code reaches raw memory through `fly.mem`. *(Done in 0.15.)*
12. Then power: first-class functions and closures, bounded generic
    interfaces, pattern matching over enums.

**Exit:** no undefined or unchecked behaviour is reachable from user code except
through `fly.mem`.

## Phase 3 — A coherent standard library

*One model, applied everywhere.* The library is already broad — sockets, TLS on
two back ends, HTTP, JSON, gzip and tar in pure Fly, SHA-256. What it lacks is
uniformity.

1. **One error model.** Every module reports failure with `fail` and documented
   codes. Sentinel values, `errOut` parameters and silent zero values go away;
   an out-of-range `List.get` fails.
2. **Typed handles.** `Socket`, `TlsConn`, `JsonValue`, `PollSet`, `Process`
   replace opaque `pointer` and `long` values.
3. **Collections.** Iteration over every container, sorting, searching,
   `keys()`/`values()`; a balanced `Tree`; seeded hashing.
4. **Text.** `split`, `join`, formatting, number parsing, a string builder,
   UTF-8.
5. **Networking.** IPv6 and `getaddrinfo`; typed HTTP requests and responses
   with size limits; one client that selects TLS from the URL scheme; a server
   loop.
6. **Security.** A cryptographic random source, HMAC, SHA-512, constant-time
   comparison; depth and expansion limits in the JSON and inflate decoders.
7. **Concurrency.** `join`, condition variables, public atomics, channels; the
   thread-safety of every type documented.
8. **Processes and time.** Pipes and in-memory capture, child environment and
   time-outs; time zones.
9. **New modules.** Formatting, regular expressions, argument parsing,
   cryptography, UUID, CSV.
10. **Quality.** Fuzzing of every parser; reference benchmarks; an automated
    check that every public function is documented.

**Exit:** no public function reports failure by a sentinel or a silent default.

## Phase 4 — Ecosystem and 1.0

- A formatter, a documentation generator, test coverage.
- Signed packages and audited dependencies in the registry.
- A normative **specification with a conformance suite** as the source of truth.
- A stability policy: semantic versioning, editions for incompatible changes,
  deprecation with one release of notice.

### Criteria for 1.0

- The stage 3 fixpoint holds on every supported target.
- The specification, the conformance suite and the compiler agree, with no
  undocumented divergence.
- No unchecked behaviour is reachable from user code except through `fly.mem`.
- The standard library uses a single error model.
- The documentation is generated from, and verified against, the sources.
