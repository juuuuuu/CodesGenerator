# Codes Generator

A simple Symfony Console component codes generator based on Xorshift.

## Usage

You just need to specify a _seed_ to instantiate the generator.

```bash
php app.php generate:number <seed>
```

## Resource

[Memcached](https://memcached.org/) is needed.  
Based on [StickyBeat's XorShift](https://github.com/StickyBeat/pseudo-random-generator-xor-shift) pseudo-random generator.  
