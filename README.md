# Unique Codes Generator

A simple Symfony Console component codes generator based on Xorshift. This can be used to generate in pack product games codes such as `JD87QYDH` or `MADNDY5DDZ0`, etc.

## Usage

You need to specify a _number_ of codes to generate and a _seed_ to instantiate the generator.

```bash
php app.php generate:codes <number> <seed>
```

## Resource

Based on [StickyBeat's XorShift](https://github.com/StickyBeat/pseudo-random-generator-xor-shift) pseudo-random generator.  
