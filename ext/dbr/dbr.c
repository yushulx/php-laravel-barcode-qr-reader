/* dbr extension for PHP */

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "php_dbr.h"

#include "DynamsoftBarcodeReader.h"

// #if defined(_WIN64)
// #pragma comment(lib, "DBRx64.lib")
// #endif

/* For compatibility with older PHP versions */
#ifndef ZEND_PARSE_PARAMETERS_NONE
#define ZEND_PARSE_PARAMETERS_NONE() \
	ZEND_PARSE_PARAMETERS_START(0, 0) \
	ZEND_PARSE_PARAMETERS_END()
#endif

static void *hBarcode = NULL;

#define CHECK_DBR() 										\
if (!hBarcode) 												\
{															\
	hBarcode = DBR_CreateInstance();						\
	const char* versionInfo = DBR_GetVersion();				\
	printf("Dynamsoft Barcode Reader %s\n", versionInfo);	\
}


PHP_FUNCTION(DBRInitLicense)
{
	CHECK_DBR();

	char *pszLicense;
	size_t iLen;
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &pszLicense, &iLen) == FAILURE)
	{
		RETURN_STRING("Invalid parameters");
	}
	char errorMsgBuffer[512];
	// Click https://www.dynamsoft.com/customer/license/trialLicense/?product=dbr to get a trial license.
	DBR_InitLicense(pszLicense, errorMsgBuffer, 512);
	printf("DBR_InitLicense: %s\n", errorMsgBuffer);
}

PHP_FUNCTION(DBRInitRuntimeSettingsWithFile)
{
	CHECK_DBR();

	array_init(return_value);

	char *pszFilePath;
	size_t iLen;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &pszFilePath, &iLen) == FAILURE)
	{
		RETURN_STRING("Invalid parameters");
	}

	char errorBuffer[512];

	DBR_InitRuntimeSettingsWithFile(hBarcode, pszFilePath, CM_OVERWRITE, errorBuffer, 512);

	add_next_index_string(return_value, errorBuffer);

}

PHP_FUNCTION(DBRInitRuntimeSettingsWithString)
{
	CHECK_DBR();

	array_init(return_value);

	char *pszContent;
	size_t iLen;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &pszContent, &iLen) == FAILURE)
	{
		RETURN_STRING("Invalid parameters");
	}

	char errorBuffer[512];

	DBR_InitRuntimeSettingsWithString(hBarcode, pszContent, CM_OVERWRITE, errorBuffer, 512);

	add_next_index_string(return_value, errorBuffer);

}

PHP_FUNCTION(DecodeBarcodeFile)
{
	CHECK_DBR();

	array_init(return_value);

	// Get Barcode image path
	char *pFileName;
	long barcodeType = 0;
	size_t iLen;
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sl", &pFileName, &iLen, &barcodeType) == FAILURE)
	{
		RETURN_STRING("Invalid parameters");
	}

	if (hBarcode)
	{
		int iMaxCount = 0x7FFFFFFF;
		TextResultArray *pResults = NULL;

		// Update DBR params
		PublicRuntimeSettings pSettings = {0};
		DBR_GetRuntimeSettings(hBarcode, &pSettings);
		pSettings.barcodeFormatIds = barcodeType;
		char szErrorMsgBuffer[256];
		DBR_UpdateRuntimeSettings(hBarcode, &pSettings, szErrorMsgBuffer, 256);

		// Barcode detection
		int ret = DBR_DecodeFile(hBarcode, pFileName, "");
		DBR_GetAllTextResults(hBarcode, &pResults);
		if (pResults)
		{
			int count = pResults->resultsCount;
			int i = 0;
			char strLocalization[128];
			for (; i < count; i++)
			{
				zval tmp_array;
				array_init(&tmp_array);
				add_next_index_string(&tmp_array, pResults->results[i]->barcodeFormatString);
				add_next_index_string(&tmp_array, pResults->results[i]->barcodeText);
				add_next_index_stringl(&tmp_array, pResults->results[i]->barcodeBytes, pResults->results[i]->barcodeBytesLength);

				memset(strLocalization, 0, 128);
				sprintf(strLocalization, "[(%d,%d),(%d,%d),(%d,%d),(%d,%d)]", \
				pResults->results[i]->localizationResult->x1, pResults->results[i]->localizationResult->y1, \
				pResults->results[i]->localizationResult->x2, pResults->results[i]->localizationResult->y2, \
				pResults->results[i]->localizationResult->x3, pResults->results[i]->localizationResult->y3, \
				pResults->results[i]->localizationResult->x4, pResults->results[i]->localizationResult->y4); 
				add_next_index_string(&tmp_array, strLocalization);

				add_next_index_zval(return_value, &tmp_array);
			}
			DBR_FreeTextResults(&pResults);
		}
	}
}

PHP_FUNCTION(DBRCreate)
{
	CHECK_DBR();
}

PHP_FUNCTION(DBRDestroy)
{
	if (hBarcode)
	{
		DBR_DestroyInstance(hBarcode);
		hBarcode = NULL;
	}
}

/* }}}*/

/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(dbr)
{
#if defined(ZTS) && defined(COMPILE_DL_DBR)
	ZEND_TSRMLS_CACHE_UPDATE();
#endif

	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(dbr)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "dbr support", "enabled");
	php_info_print_table_end();
}
/* }}} */

/* {{{ arginfo
 */
ZEND_BEGIN_ARG_INFO(arginfo_dbr_test1, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_dbr_test2, 0)
	ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()
/* }}} */

/* {{{ dbr_functions[]
 */
static const zend_function_entry dbr_functions[] = {
	PHP_FE(DBRInitLicense, NULL)
	PHP_FE(DBRInitRuntimeSettingsWithFile, NULL)
	PHP_FE(DBRInitRuntimeSettingsWithString, NULL)
	PHP_FE(DecodeBarcodeFile, NULL)
	PHP_FE(DBRCreate, NULL)
	PHP_FE(DBRDestroy, NULL)
	PHP_FE_END
};
/* }}} */

/* {{{ dbr_module_entry
 */
zend_module_entry dbr_module_entry = {
	STANDARD_MODULE_HEADER,
	"dbr",					/* Extension name */
	dbr_functions,			/* zend_function_entry */
	NULL,							/* PHP_MINIT - Module initialization */
	NULL,							/* PHP_MSHUTDOWN - Module shutdown */
	PHP_RINIT(dbr),			/* PHP_RINIT - Request initialization */
	NULL,							/* PHP_RSHUTDOWN - Request shutdown */
	PHP_MINFO(dbr),			/* PHP_MINFO - Module info */
	PHP_DBR_VERSION,		/* Version */
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_DBR
# ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
# endif
ZEND_GET_MODULE(dbr)
#endif
