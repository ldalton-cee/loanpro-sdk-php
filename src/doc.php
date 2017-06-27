<?php


/*! \mainpage LoanPro PHP SDK
 *
 * \tableofcontents
 *
 * \section intro_sec Introduction
 *
 * The goal of the LoanPro PHP SDK is to abstract the complexity of the LoanPro system and allow developers to create fast applications. This is accomplished by abstracting OData entities into PHP classes and providing a list of properties for each class. This list of constants allows integrating code to not have to change in the event of a property name change. This means that if all properties called "active" are renamed to "isActive", the constants list will be updated and integrating code will work once the new SDK is installed. Furthermore, the SDK does a lot of validation and input sanitization, as well as error parsing. Also, it provides several methods for credential management as well as integrating with both production and staging environments.
 *
 * To show how simple it is to use the SDK, below is a sample of creating a modification for a loan and doubling the lending amount:
 *
 * ```php
 * use \Simnang\Loanpro\Constants\LOAN, \Simnang\Loanpro\Constants\LOAN_SETUP, \Simnang\Loanpro\Loans\LoanSetupEntity;
 * $loan = LoanProSDK::GetInstance()->GetLoan(55, [LOAN::LOAN_SETUP]);
 * $lsetup = $loan->Get(LOAN::LOAN_SETUP);
 * $loan->createModification($lsetup->Set(LOAN_SETUP::LOAN_AMT, $lsetup->Get(LOAN_SETUP::LOAN_AMT) / 2));
 * ```
 *
 * Below is an example of halving the loan amount, discount, and interest rate for another loan and then saving the changes to the server:
 *
 * ```php
 * use \Simnang\Loanpro\Constants\LOAN, \Simnang\Loanpro\Constants\LOAN_SETUP, \Simnang\Loanpro\Loans\LoanSetupEntity;
 * $halve = function($a){ return $a / 2; };
 * $loan = LoanProSDK::GetInstance()->GetLoan(55, [LOAN::LOAN_SETUP]);
 * $lsetup = $loan->Get(LOAN::LOAN_SETUP);
 * $loan->Set(LOAN::LOAN_SETUP, $lsetup->Set(array_map($halve,$lsetup->Get(LOAN_SETUP::LOAN_AMT, LOAN_SETUP::DISCOUNT, LOAN_SETUP::LOAN_RATE))))->save();
 * ```
 *
 * When using the SDK, it is important to remember that the Set, Rem, and Del functions don't change the entity, instead they returns a modified copy of an entity. This allows all entities to act as prototypes for creating more entities.
 *
 * Below is an example of how the Set function works:
 *
 * ```php
 * use \Simnang\Loanpro\Constants\LOAN;
 * $loan = LoanProSDK::GetInstance()->GetLoan(55, [LOAN::LOAN_SETUP])->Set(LOAN::DISP_ID,'initial');
 * $loan->Set(LOAN::DISP_ID, 'foo');
 * echo $loan->Get(LOAN::DISP_ID); // echos 'initial'
 * $loan = $loan->Set(LOAN::DISP_ID, 'bar');
 * echo $loan->Get(LOAN::DISP_ID); // echos 'bar'
 * echo $loan->Set(LOAN::DISP_ID,'foobar')->Get(LOAN::DISP_ID); // echos 'foobar'
 * echo $loan->Get(LOAN::DISP_ID); // echos 'bar'
 * ```
 *
 * \section get_started_sec Getting Started
 *
 * To get started with the PHP SDK, we recommend that you start with one of the following resources:
 *
 * * <a href="http://api.loanprosoftware.com/php_sdk_videos.html">PHP SDK videos</a>
 * * \subpage get_started "Getting Started"
 * * <a href="http://api.loanprosoftware.com/apiSlides">PHP SDK Presentations</a>
 *
 * \section ref_docs Reference Documentation
 *
 * We have a lot of reference documentation to help you learn more about what is offered. Below is a list of all reference documentation available:
 *
 * * \ref mainpage "Doxygen Docs"
 * * <a href="http://api.loanprosoftware.com/elasticsearchDocs/">Elasticsearch Docs</a>
 * * <a href="http://api.loanprosoftware.com/databaseDocs/">Database Docs</a>
 * * <a href="https://help-loanpro.simnang.com/article-categories/api/">API articles</a>
 * * <a href="https://help-loanpro.simnang.com/article-categories/php-sdk/">PHP SDK articles</a>
 *
 */



/*! \page get_started Getting Started
 *
 * \section intro_sec Installing
 *
 * To install the SDK, download it from the <a href="https://github.com/autopalsoftware/loanpro-sdk-php" target="_blank">GitHub repo</a>, and then run setup.sh. When prompted, enter your tenant ID and API token.
 *
 * Alternatively, you can use Composer. Add the following to your composer file:
 *
 * ```json
    "repositories":[
        {
            "url":"https://github.com/autopalsoftware/loanpro-sdk-php.git",
            "type":"git"
        }
    ],
    "require":{
        "simnang/loanpro-sdk":"3.*"
    },
 * ```
 *
 * Then, update composer. Once composer has updated, navigate to `vendor/simnang/loanpro-sdk` and run the setup.sh script (enter tenant id and token when prompted; don't run composer).
 *
 * For more advanced configuration, see \ref conf_sdk
 *
 * \section include_in_code Including in Your Code
 *
 * To include the SDK in your code, include your autoload file (if installed manually, it'll be in the repo directory/vendor/autoload.php; otherwise it's vendor/autoload.php). Then add `use \Simnang\Loanpro\LoanProSDK`. This will include the LoanProSDK file. To use the SDK, just grab the instance of the LoanProSDK class with `LoanProSDK::GetInstance()`. Below is an example:
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\Loanpro\LoanProSDK;
 *
 * $sdk = LoanProSDK::GetInstance();
 * ```
 *
 * \section loan_create Creating a loan
 *
 * To create a loan in LoanPro, you will need to create a LoanEntity and LoanSetupEntity, and then save to the server.
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\Loanpro\LoanProSDK,
 *     \Simnang\Loanpro\Constants\LOAN_SETUP,
 *     \Simnang\Loanpro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C as LOAN_SETUP_LCLASS,
 *     \Simnang\Loanpro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C as LOAN_SETUP_LTYPE;
 *
 * $sdk = LoanProSDK::GetInstance();
 * $loanSetup = $sdk->CreateLoanSetup(LOAN_SETUP_LCLASS::CAR, LOAN_SETUP_LTYPE::INSTALLMENT)
 *                  ->Set([LOAN_SETUP::LOAN_AMT=>36000, LOAN_SETUP::DISCOUNT=> 1400, LOAN_SETUP::UNDERWRITING=> 800]);
 * $loan = $sdk->CreateLoan("DISP_ID_001", $loanSetup);
 * $loan->Save();
 * ```
 *
 * \section pull_loan Pulling a Loan
 *
 * To pull a loan from the server, you just need to use the GetLoan function in the LoanProSDK class. The GetLoan function takes the ID of the loan to grab. The function also has an optional $expands array which is a list of nested entities to expand. Below is an example:
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\Loanpro\LoanProSDK,
 *     \Simnang\Loanpro\Constants\LOAN;
 *
 * $loan = LoanProSDK::GetInstance()->GetLoan(5, [LOAN::LOAN_SETUP]);
 *
 * ```
 *
 * \section pull_loans Pulling Multiple Loans
 *
 * To get multiple loans in the system, you can use the GetLoans function in the LoanProSDK class. The GetLoans function returns an iterator for the loans retrieved from the system. This function takes an array of properties to expand and a filter parameter. The filter parameter will determine which loans to match against (see \ref filtering for more information). By default, this function will get all loans from LoanPro. Below is an example:
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\Loanpro\LoanProSDK,
 *     \Simnang\Loanpro\Constants\LOAN;
 *
 * $loans = LoanProSDK::GetInstance()->GetLoans([LOAN::LOAN_SETUP]);
 * foreach($loans as $loan){
 *  // Do something with the loans
 * }
 * ```
 *
 * The iterator returned is also compatible with YaLinqo, a PHP version of C#'s Linq. Below is an example of how to use Linq to count loans whose amount is greater than $15,000.00.
 *
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\Loanpro\LoanProSDK,
 *     \Simnang\Loanpro\Constants\LOAN_SETUP,
 *     \Simnang\Loanpro\Constants\LOAN;
 *
 *
 * $res = from(LoanProSDK::GetInstance()->GetLoans([LOAN::LOAN_SETUP]))
 *        ->where(function($loan){ return $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LOAN_AMT) > 15000.00;})->count();
 * ```
 *
 */


/*! \page filtering Filtering
 *
 * In the LoanPro SDK, filtering is done by use of the FilterParams object. There are three ways to make a FilterParams object: MakeFromODataString, MakeFromODataString_UNSAFE, and MakeFromLogicString. Each option has its own benefits and drawbacks.
 *
 * \section filtering_odata MakeFromODataString
 *
 * The MakeFromODataString function takes a filter string that is in the form specified by the OData standard. Before creation, it will perform basic linting to make sure that it is in a valid format. These checks do have runtime overhead, so they shouldn't be used with queries that you have verified will always be correct (such as hard-coded queries that don't take variables for parameters). It also has no guarantee that a string is in the proper format, but it does provide enough tests to catch most errors.
 *
 * \section filtering_odata_unsafe MakeFromODataString_UNSAFE
 *
 * The MakeFromODataString_UNSAFE function takes a string in the form of the OData standard. It does not perform any checks and assumes the filter is correct. This should not be used with code that has not been tested or that is directly dependent upon user-input. Instead, use MakeFromODataString to catch common linting errors before sending it off to the server.
 *
 * \section filtering_logic MakeFromLogicString
 *
 * The MakeFromLogicString function takes a string in more C/C++/PHP/C# style formatting and then generates a valid OData string. It is the slower of the three options, but uses more concise and familiar syntax. Below is a list of the operators allowed:
 *
 * * **!** - The not operator, negates an expression
 * * <b>=,==,&lt;,&lt;=,&gt;,&gt;=,!=</b> - Comparison operators, compares two values
 * * <b>&amp;,&amp;&amp;</b> - The and operator, makes sure that the left and right hand sides are true
 * * <b>|,||</b> - The or operator, makes sure that either the left or right hand side are true (or both)
 * * <b>+,-,\*,/,%</b> -  Arithmetic operators, performs arithmetic on two values
 *
 * Example:
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\LoanPro\Iteration\FilterParams,
 *     \Simnang\LoanPro\Constants\BASE_ENTITY;
 *
 * // Checks if 4 + 3 is not equal to 8
 * $filter = FilterParams::MakeFromLogicString('4 + 3 != 8');
 *
 * // Checks if the id is less than 200
 * $filter = FilterParams::MakeFromLogicString(BASE_ENTITY::ID.' < 200');
 *
 * // Combines the above two examples
 * $filter = FilterParams::MakeFromLogicString('4 + 3 != 8 && '.BASE_ENTITY::ID.' < 200');
 * ```
 *
 *
 */

/*! \page conf_sdk Configuring the SDK
 *
 * The LoanProSDK comes with a powerful and flexible configuration system. By default, it loads all of the configuration information from the config.ini file in the SDK's src directory (this is created by the setup.sh script). This page goes over the structure of the config.ini file, how to change where the configuration is loaded, and how to set the configuration in code.
 *
 * \section def_conf Configuration File Format
 *
 * By default, the configuration file is in the repo directory's src file (in a manual install it's repo-directory/src/config.ini, in a composer install it's vendor/simnang/loanpro-sdk/src/config.ini). This config file is in the INI format. It has the following sections:
 *
 * * api
 * * communicator
 * * config
 *
 * These different sections control API authorization, communication method to LoanPro, and configuration settings respectively.
 *
 * \subsction api_sec API Section
 *
 * The API section of the config file holds the information about how to authenticate with the LoanPro API. It holds two key-value pairs: tenant (your tenant id) and token (your API token).
 *
 * These sections should not have any whitespace. An example is shown below:
 *
 * ```ini
 * [api]
 * tenant=1
 * token=abcdefg1234567
 * ```
 *
 * \subsction com_sec Communicator Section
 *
 * The communicator section of the config file holds information about how the SDK communicates with LoanPro. This includes target environment (eg. production or staging) as well as whether or not the PSR-7 compatible communicator is synchronous or asynchronous (this doesn't affect code execution, just how the SDK wraps the communication channel; by default the CURL PSR-7 communicator is used).
 *
 * The key-value pairs of this section are: env (the target LoanPro environment, possible values are **prod** and **staging**) and  type (type of PSR-7 communicator, can be **async** or **sync**)
 *
 * \section conf_sec More Configuration files
 *
 * The config section holds information about where to look for overriding configuration files. The system will then look to see if the specified file exists; if so it will load the settings found in that file. Settings found in loaded config files will override previously loaded settings. This means that the last file of the chain will dominate the first file in the chain; this allows for safe defaults to fall back to in case a file is missing or does not contain needed information.
 *
 * For extra config files, the SDK can load XML, JSON, or INI files (the main config.ini file must remain an INI file). To specify a file to load, add a **file** key-value pair with the value being the path to the file to load. The SDK will then determine the file type based off of the extension (it will look to see if the extension is `json`, `xml`, or `ini`); if you want/need to specify the extension manually just add the key-value pair of **type** with the value of xml, json, or ini to specify the file type.
 *
 * Below is an example:
 *
 * ```ini
 * [config]
 * file=/config.conf
 * type=json
 * ```
 *
 * It is important to note that the SDK will only load up to 9 (nine) additional configuration files (not including the default configuration file). This is to prevent infinite-looping and to provide a termination point to limit time spent in initialization.
 *
 * \subsection file_form File Formats
 *
 * The JSON and XML files follow an almost identical structure to the INI file.
 *
 * For JSON files, there is a JSON object whose children are the sections. Key-value pairs are stored in the section objects. Below is an example:
 *
 * ```json
 * {
 *   "communicator":{
 *       "env":"staging",
 *       "type":"async"
 *   },
 *   "config":{
 *       "file":"config.xml"
 *   }
 * }
 * ```
 *
 * For XML files, the root tag must be **config**, after which the structure is similar to JSON in that sections are children of the root tag and key-value pairs are children of the sections. Below is an example:
 *
 * ```xml
 * <config>
 *   <api>
 *       <token>abcdefg1234567</token>
 *   </api>
 *   <config>
 *       <file>
 *           config.ini
 *       </file>
 *   </config>
 * </config>
 * ```
 *
 * \section conf_code Setting Configuration with Code
 *
 * If you do not want to setup configuration using a config file, there is the option to set it with code. To do this, call the static SetConfig function in the LoanProSDK class. The function takes the following arguments:
 * * $commType - The type of PSR-7 communicator (async or sync)
 * * $env - The LoanPro environment to communicate with (prod or staging)
 * * $tenant - The tenant ID
 * * $token - The tenant token
 *
 * Example:
 *
 * ```php
 * require('vendor/autoload.php');
 * use \Simnang\Loanpro\LoanProSDK;
 *
 * $sdk = LoanProSDK::SetConfig('sync', 'staging', '1', 'abcdefg1234567');
 * ```
 *
 *
 * The amount of information you send and the ordering with when you first get the LoanPro instance will determine what is loaded from config files and what is set by code.
 *
 * If you set all parameters (including tenant id and token), then the configuration set in code will override the configuration found in config files. If you call GetInstance first, then the system will load the configuration files and then disregard that information. If you call SetConfig first, then the SDK won't even try to load config files.
 *
 * If you don't set the tenant id and token, then what happens is dependant upon whether or not GetInstance has been called. If GetInstance has been called, then the system will only change the $commType and $environment, the tenant ID and token won't be used. If GetInstance has not yet been called, then the SDK will ignore what you set in code and load from the config files.
 *
 * The reason for this is that the SDK will look for a valid configuration (minimum of a tenant id and token). If a valid configuration is not found, then it will assume its current configuration is corrupt and try to find a configuration on its own. Otherwise, the SDK will assume it has a valid configuration and only tweak as needed.
 *
 * If you aren't sure how much information to give when configuring in code, just give all the information you can. That will ensure the least amount of bugs in your code.
 *
 */
