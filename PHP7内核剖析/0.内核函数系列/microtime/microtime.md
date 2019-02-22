# microtime

> **Description**[ ¶](http://php.net/manual/en/function.microtime.php#refsect1-function.microtime-description)
>
> microtime ([ bool `$get_as_float` = **FALSE** ] ) : [mixed](http://php.net/manual/en/language.pseudo-types.php#language.types.mixed)
>
> microtime() returns the current Unix timestamp with microseconds. This function is only available on operating systems that support the gettimeofday() system call.
>
> source file :/php-src/ext/standard/microtime.c

## Part-1 :  内核抛出定义

### 1.1 源码

```c
85  /* {{{ proto mixed microtime([bool get_as_float])
86     Returns either a string or a float containing the current time in seconds and microseconds */
87  PHP_FUNCTION(microtime)
88  {
89  	_php_gettimeofday(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
90  }
91  /* }}} */
```

## Part-2 : 源码分析

### 2.1 源码

```c
49  static void _php_gettimeofday(INTERNAL_FUNCTION_PARAMETERS, int mode)
50  {
51  	zend_bool get_as_float = 0;
52  	struct timeval tp = {0};
53  
54  	ZEND_PARSE_PARAMETERS_START(0, 1)
55  		Z_PARAM_OPTIONAL
56  		Z_PARAM_BOOL(get_as_float)
57  	ZEND_PARSE_PARAMETERS_END();
58  
59  	if (gettimeofday(&tp, NULL)) {
60  		RETURN_FALSE;
61  	}
62  
63  	if (get_as_float) {
64  		RETURN_DOUBLE((double)(tp.tv_sec + tp.tv_usec / MICRO_IN_SEC));
65  	}
66  
67  	if (mode) {
68  		timelib_time_offset *offset;
69  
70  		offset = timelib_get_time_zone_info(tp.tv_sec, get_timezone_info());
71  
72  		array_init(return_value);
73  		add_assoc_long(return_value, "sec", tp.tv_sec);
74  		add_assoc_long(return_value, "usec", tp.tv_usec);
75  
76  		add_assoc_long(return_value, "minuteswest", -offset->offset / SEC_IN_MIN);
77  		add_assoc_long(return_value, "dsttime", offset->is_dst);
78  
79  		timelib_time_offset_dtor(offset);
80  	} else {
81  		RETURN_NEW_STR(zend_strpprintf(0, "%.8F %ld", tp.tv_usec / MICRO_IN_SEC, (long)tp.tv_sec));
82  	}
83  }
84  
85  /* {{{ proto mixed microtime([bool get_as_float])
86     Returns either a string or a float containing the current time in seconds and microseconds */
87  PHP_FUNCTION(microtime)
88  {
89  	_php_gettimeofday(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
90  }
91  /* }}} */
```

### 2.2 源码详解

首先在87行是Part-1部分提到的PHP引擎对外抛出函数,此处通过PHP_FUNCTION宏定义让PHP引擎拥有过的内核函数microtime . 

从49行开始是_php_gettimeofday函数的具体实现函数,具体解析如下:

1.  54-57行: 从PHP函数调用处解析可选参数get_as_float
2.  59-61行: 调用操作系统内核函数gettimeofday获取时间信息 ([链接)](https://linux.die.net/man/2/gettimeofday)
3.  63-65行: 如果get_as_float为true则返回double给用户(此处需要注意编译器的bit数量,32和64位精度不一样)
4.  67-82行: 如果mode为false则构造字符串数据并返回,如果为true则构造数组并返回.

## Part-2 : 注意

**牵扯到64浮点数据 , 一定要注意编译器的位数 , 精度的不同会造成意想不到的BUG.** 你函数能够返回Double类型,要注意.