## Description

PHP Library for interacting with the Stellar network.

* Communicate with Horizon server
* Build and sign transactions

## :warning: Danger Zone :warning:

**Development Status**

This library is under active development and should be considered beta quality.
Please ensure that you've tested extensively on a test network and have added
sanity checks in other places in your code.

:warning: [See the 0.2.0 release notes for breaking changes](CHANGELOG.md) 

**Large Integer Support**

The largest PHP integer is 64-bits when on a 64-bit platform. This is especially
important to pay attention to when working with large balance transfers. The native
representation of a single XLM (1 XLM) is 10000000 stroops.

Therefore, if you try to use a `MAX_INT` number of XLM (or a custom asset) it is
possible to overflow PHP's integer type when the value is converted to stroops and
sent to the network.

This library attempts to add checks for this scenario and also uses a `BigInteger`
class to work around this problem.

If your application uses large amounts of XLM or a custom asset please do extensive
testing with large values and use the `StellarAmount` helper class or the `BigInteger` 
class if possible.

**Floating point issues**

Although not specific to Stellar or PHP, it's important to be aware of problems
when doing comparisons between floating point numbers.

For example:

```php
$oldBalance = 1.605;
$newBalance = 1.61;

var_dump($oldBalance + 0.005);
var_dump($newBalance);
if ($oldBalance + 0.005 === $newBalance) {
    print "Equal\n";
}
else {
    print "Not Equal\n";
}
```

The above code considers the two values not to be equal even though the same value
is printed out:

Output:
```
float(1.61)
float(1.61)
Not Equal
```

To work around this issue, always work with and store amounts as an integer representing stroops. Only convert
back to a decimal number when you need to display a balance to the user.

The static `StellarAmount::STROOP_SCALE` property can be used to help with this conversion.

## Getting Started

See the [getting-started](getting-started/) directory for examples of how to use this library.

These examples are modeled after the ones in Stellar's getting started guide:

https://www.stellar.org/developers/guides/get-started/create-account.html

Additional examples are available in the [examples](examples/) directory 

## Donations

Stellar: GCUVDZRQ6CX347AMUUWZDYSNDFAWDN6FUYM5DVYYVO574NHTAUCQAK53
