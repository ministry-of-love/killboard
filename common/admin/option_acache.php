<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */

/*
* This file contains the generic admin options in the new format
* look here for some examples.
*/
options::cat('Advanced', 'Cache', 'Page Cache');
options::fadd('Enable Page Cache', 'cache_enabled', 'checkbox','', array('admin_acache', 'clearPCache'), "Cache created webpages");
options::fadd('Global lifetime', 'cache_time', 'edit:size:4','','','minutes');
options::fadd('Ignore pages', 'cache_ignore', 'edit:size:60','','','page1,page2 [no spaces]');
options::fadd('Page specific lifetime', 'cache_times', 'edit:size:60','','','page:time,page2:time [no spaces]');
options::fadd('Pages updated every kill', 'cache_update', 'edit:size:60','','','* or page1,page2 [no spaces]');

options::cat('Advanced', 'Cache', 'Query Cache');
options::fadd('Enable SQL-Query File Cache', 'cfg_qcache', 'checkbox', '', '','Select only one of file cache or memcache');
options::fadd('Enable SQL-Query MemCache', 'cfg_memcache', 'checkbox','','','Requires a separate memcached installation');
options::fadd('Memcached server', 'cfg_memcache_server', 'edit:size:50');
options::fadd('Memcached port', 'cfg_memcache_port', 'edit:size:8');
options::fadd('Enable SQL-Query Redis', 'cfg_redis', 'checkbox','','','Requires a separate Redis installation');
options::fadd('Redis server', 'cfg_redis_server', 'edit:size:50');
options::fadd('Redis port', 'cfg_redis_port', 'edit:size:8');
options::fadd('Redis database', 'cfg_redis_db', 'edit:size:8');
options::fadd('Halt on SQLError', 'cfg_sqlhalt', 'checkbox');

options::cat('Advanced', 'Cache', 'Even More Caching');
options::fadd('Killmail Caching enabled','km_cache_enabled','checkbox');
options::fadd('Object Caching enabled','cfg_objcache','checkbox','','','Advisable for memcached boards.');

options::cat('Advanced', 'Cache', 'Clear Caches');
options::fadd('File Cache', 'none', 'custom', array('admin_acache', 'optionClearCaches'), array('admin_acache', 'clearCaches'));
options::fadd('Kill Summary Cache', 'none', 'custom', array('admin_acache', 'optionClearSum'), array('admin_acache', 'clearSumCache'));

global $mc;
global $redis;
if(defined('DB_USE_MEMCACHE') && DB_USE_MEMCACHE == true && !is_null($mc) && method_exists('Memcache', 'flush'))
{
    options::fadd('MemCache', 'none', 'custom', array('admin_acache', 'optionFlushMemcached'), array('admin_acache', 'flushMemcached'));
}
if(defined('DB_USE_REDIS') && DB_USE_REDIS == true && !is_null($redis) && method_exists('Redis', 'flushDb'))
{
    options::fadd('Redis', 'none', 'custom', array('admin_acache', 'optionFlushRedis'), array('admin_acache', 'flushRedis'));
}

class admin_acache
{
        static function getKillmails()
        {
                    $count = 0;
                    if(defined('KB_MAILCACHEDIR'))
                    {
                            if(is_dir(KB_MAILCACHEDIR))
                            {
                                    if($files = scandir(KB_MAILCACHEDIR))
                                    {
                                            foreach($files as $file)
                                            {
                                                    if (substr($file, 0, 1) != '.') $count++;
                                            }

                                    }
                            }
                    }
                    return $count;
        }
        
        static function clearPCache()
        {
                    CacheHandler::removeByAge('store', 0);
        }
        
	static function optionClearCaches()
	{
		return '<input type="checkbox" name="option_clear_caches" />Clear caches ?';
	}
	
        static function optionClearSum()
	{
		return '<input type="checkbox" name="option_clear_sum" />Clear cache ?';
	}
        
        static function optionFlushMemcached()
	{
		return '<input type="checkbox" name="option_flush_memcached" />Flush MemCache ?';
	}
	
        static function optionFlushRedis()
	{
		return '<input type="checkbox" name="option_flush_redis" />Flush Redis ?';
	}
	
        static function clearCaches()
	{
                if ($_POST['option_clear_caches'] == 'on') {
                                CacheHandler::removeByAge('data', 0, true);
                                CacheHandler::removeByAge('api', 0, true);
                                CacheHandler::removeByAge('store', 0, true);
                                CacheHandler::removeByAge('mails', 0, true);
                                CacheHandler::removeByAge('img', 0, true);
                                CacheHandler::removeByAge('templates_c', 0);
                                CacheHandler::removeByAge('SQL', 0, true);
                                $_POST['option_clear_caches'] = 'off';
                }
	}
	static function clearSumCache()
	{
                if ($_POST['option_clear_sum'] == 'on') {
                                $qry = DBFactory::getDBQuery();;
                                $qry->execute("DELETE FROM kb3_sum_alliance");
                                $qry->execute("DELETE FROM kb3_sum_corp");
                                $qry->execute("DELETE FROM kb3_sum_pilot");
                                // Clear page and query cache as well since they also contain the
                                // summaries.
                                CacheHandler::removeByAge('SQL', 0, true);
                                CacheHandler::removeByAge('store', 0, true);
                                $_POST['option_clear_sum'] == 'off';
                }
	}
    
	static function flushMemcached()
	{
            global $mc;
            // Check for memcached
            if(defined('DB_USE_MEMCACHE') && DB_USE_MEMCACHE == true && !is_null($mc) && method_exists('Memcache', 'flush'))
            {
                $mc->flush();
            }
            $_POST['option_flush_memcached'] = 'off';
	}
	
	static function flushRedis()
	{
            global $redis;
            // Check for Redis
            if(defined('DB_USE_REDIS') && DB_USE_REDIS == true && !is_null($redis) && method_exists('Redis', 'flushDb'))
            {
                $redis->flushDb();
            }
            $_POST['option_flush_redis'] = 'off';
	}
}