/* dbr extension for PHP */

#ifndef PHP_DBR_H
# define PHP_DBR_H

extern zend_module_entry dbr_module_entry;
# define phpext_dbr_ptr &dbr_module_entry

# define PHP_DBR_VERSION "0.1.0"

# if defined(ZTS) && defined(COMPILE_DL_DBR)
ZEND_TSRMLS_CACHE_EXTERN()
# endif

#endif	/* PHP_DBR_H */
