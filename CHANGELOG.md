### 0.2.0

**Breaking Changes / Incompatibilities**
 * **IMPORTANT**: arguments to `PaymentOp` have changed (the destination is now the first argument and source is an optional argument) 
 * `Server->getAccount()` now returns `null` if an account is not found. Previously,
 it threw an exception.
 * `CreateAccountXdrOperation` has been deprecated and replaced with `CreateAccountOp`.
 It will be removed in version 1.2.0
 * `PaymentOp::newNativePayment` now has `sourceAccountId` as an optional argument that comes last
 * `PaymentOp::newCustomPayment` now has `sourceAccountId` as an optional argument that comes last
 * `TransactionBuilder::addCustomAssetPaymentOp` now has `sourceAccountId` as an optional argument that comes last
 
New Features
 * All operations are now supported by `TransactionBuilder`
 * Better error handling with `HorizonException` class that provides detailed
 information pulled from Horizon's REST API error messages.
 * Added `StellarAmount` class for working with numbers that may exceed PHP integer limits
 * General improvements to large number checks and more tests added 

### 0.1.0
 * Initial beta version