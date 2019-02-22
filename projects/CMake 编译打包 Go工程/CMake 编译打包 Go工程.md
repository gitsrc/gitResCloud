# 基于CMake构建标准的Go编译及打包框架

##　一. 背景描述

为了**简化团队内部中间件的编译及打包**和**方便运维伙伴的线上环境部署过程** , 我们开始尝试使用标准的rpm安装包来进行线上的环境部署,虽然制作rpm安装包可以采用很多途径 , 但是我们选择采用CMake方式 , 采用这种方式的优点如下:

1. 利用CMake 可以构建出很强大的自动编译系统.
2. CMake在编译周期结束后,支持rpm , zip等格式的自动打包.

下面的内容从两个方面来阐述: **利用CMake 构建Go语言的自动编译环境** , **利用CMake 进行rpm包制作**

## 二. 实现Go语言的通用CMake编译框架

### 2.1 文件结构

这里将构建一个具有通用性的Go语言工程编译环境,旨在为后续Go语言开发工程师,提供团队内部统一的构建基础环境, 首先我们来查看一下文件结构,文件结构的树形图如下:

![](https://raw.githubusercontent.com/gitsrc/gitResCloud/master/projects/CMake%20编译打包%20Go工程/0.png)

在这个文件结构中,我们先阐述一下相关文件的作用:

1. CMakeLists.txt : 构建系统的总入口
2. cmake目录 : 这个目录存储构建系统的子功能模块
3. cmd : 这个目录是一个功能单元的样例环境, 这里的CMakeLists.txt 被根CMakeList 加载.

### 2.2 重要文件描述

上面的工程结构,保障了CMake工程的结构化清晰,这里将对几个重要文件做详细阐述

#### 2.2.1 CMakeLists.txt

这个文件是CMake工程的总入口 , 它负责的工作包括:

* 加载cmake子目录下的子功能单元
* 定义Go工程的相关工程属性: 工程名, 工程版本号
* 加载go语言编译器
* 加载各个子编译体的源码目录
* 加载rpm , zip 等打包模块

#### 2.2.2 CMakeDetermineGoCompiler.cmake

这个文件Go语言编译器的总入口 , 它负责的工作包括:

* 在操作系统环境中寻找Go语言编译器
* 加载Go编译器参数
* 拷贝必要的编译配置文件

####　2.2.3 golang.cmake

这个文件是在CMakeDetermineGoCompiler.cmake文件基础上实现了必要的Go代码编译函数,主要功能函数包括:

* go get 支持 : 为编译所需的外部包的引入提供自动加载功能
* 编译单体程序 : 编译指定路径下的go源代码,生成单体程序体

#### 2.2.4 cpack.cmake

这个文件负责 对编译后的工程进行打包工作, 打包的格式 包括 RPM ,ZIP 等



## 三. 工程编译及打包(样例)

### 3.1 主CMakeList.txt 配置源码

```cmake
set(CMAKE_MODULE_PATH ${CMAKE_MODULE_PATH} "${CMAKE_CURRENT_SOURCE_DIR}/cmake")

cmake_minimum_required(VERSION 3.0)

set(project_version "1.0.1")

project(redtun Go) # select GO compile

include(cmake/golang.cmake)
#include(cmake/flags.cmake) if use CGO

set(output_dir ${CMAKE_CURRENT_BINARY_DIR}/bin)

# add compile source dir
add_subdirectory(cmd/RedTun-Proxy ${output_dir}) # 加载子应用体源码地址

set (default_config_uri ${output_dir})

#copy default config file to output
message("-- Move the default configuration file to the project compilation output directory.")

file(COPY ${CMAKE_CURRENT_LIST_DIR}/example/rd_config.yaml
     DESTINATION   ${default_config_uri})

#import rpm pack
set(package_type RPM)

include(cmake/cpack.cmake)
```

### 3.2 子应用CMakelist.txt 配置源码

```cmake
ExternalGoProject_Add(yaml.v2 gopkg.in/yaml.v2)

set(SOURCE_FILE yaml.v2
                main.go)

add_go_executable(RedTun ${SOURCE_FILE})
```

### 3.3 编译截图

![](https://raw.githubusercontent.com/gitsrc/gitResCloud/master/projects/CMake%20编译打包%20Go工程/1.png)

### 3.4 打包截图

![](https://raw.githubusercontent.com/gitsrc/gitResCloud/master/projects/CMake%20编译打包%20Go工程/2.png)

### 3.5 系统安装截图

![](https://raw.githubusercontent.com/gitsrc/gitResCloud/master/projects/CMake%20编译打包%20Go工程/3.png)

## 四. 附属材料

### 4.1 工程编译命令

```shell
cd "工程根目录"
mkdir build
cd build
cmake ..
make
```

编译后的文件存在build目录下的bin文件夹内

### 4.2 工程打包命令

```shell
cd build
cpack
```

### 4.3 源码地址

​	http://gitlab.shein.com:8088/NineWorlds/go-cmake



**李彪 , 徐元昌**

**2019年2月15日**