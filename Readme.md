
#Naming Conventions

## Constants

All constants are to be in ALL_CAPS with underscores between words.

## Entities

Entities are in camel case with the first letter always capitalized. It will be the name of the entity prefixing the word "Entity". 

eg.

* The Loan entity becomes "LoanEntity"
* The Settings entity becomes "LoanSettingsEntity"
* The Customer entity becomes "CustomerEntity"

## Constant Lists

Constant lists for entities are always in all caps. If its for an entity with just one word, then the name is the name of the entity. If there are multiple words, then the name is the first letters of all the words except the last word, the last word is the full word.
 
eg.

* The Loan entity becomes "LOAN"
* The Loan Settings entity becomes "LSETTINGS"
* The Loan Setup entity becomes "LSETUP"
* The Customer entity becomes "CUSTOMER"

### Fields

Fields in the constant list will always be in all caps with underscores separating words. If it is a collection field it always ends with "\_\_C", otherwise it never ends with "\_\_C" (this allows developers to quickly tell if a field is a collection list or not). 

## Collection Lists

Collection lists will be in a namespace with the constant list name for the entity (this means it's in a subdirectory named the collection list for the entity). Collection lists for entities always start with the the constant list name for the entity, followed by an underscore (\_), followed by the constant name of the field (as defined in the constant list). 
 
eg.

* For the loan class in Loan Setup, the field name is "LCLASS__C", so the collection list will be called "LSETUP_LCLASS__C".
* For the payment frequency in Loan Setup, the field name is "PAY_FREQ__C", so the collection list will be called "LSETUP_PAY_FREQ__C".

