超市会员营销制度
项目开发文档
日期：2018-10-22
温馨提示：
	请仔细阅读开发文本，系统开发以文本为主。
1、界面提供PS效果图给到客户确认审核，确认之后不做免费修改。
2、合同写的交付时间是交付测试系统，客户自己需预留4-5天用来测试以及修改。
3、为了使系统思路更严谨，在系统做出来之前一律不增加和修改文本，需要修改或者增加的功能在系统出来之后统一做二次开发和升级；
4、新增功能统一做二次开发和升级，根据二次开发的时间收取相应的工时费。
5、系统交付使用后，根据市场需求可免费帮助升级更新服务器。

系统名称：超市会员营销制度
系统域名：暂无
前台系统界面：暂无
客户端版本：手机wap
一.	概述
本系统为多商家系统平台，项目技术架构基于 PHP语言在 THINKPHP框架下的程序开发，数据库采用 Mysql 数据库，页面包括手机页面，和PC端后台。具体系统详细规则如下描述
1.	超市售价：超市商品制定正常售价、会员价。加入贵宾会员需要每年购买超市指定款产品，贵宾会员有效期为1年。（为区别超市普通会员，我们把每年购买超市指定商品定义为贵宾会员）
2.	会员推广：贵宾会员是超市的业务员，业务员可以推销超市指定的产品，每销售一款超市指定产品即可吸收一名新会员。
3.	奖金制度：贵宾会员采用三级分销奖金制度：A推荐B，B推荐C，C推荐D，当D购买制定产品消费时，C返还100元，B返还50元，A返还10元（返还金额不是固定不变的，是可以在系统设置的），会员去实体店消费报ID号即可享受优惠
4.	职级晋升：①贵宾会员推荐产品达到20套，即可成为业务主管；业务主管下面产生10名业务主管，即可升级为经理；经理下面产生6名经理即可升级为高级经理；（职级的身份是动态的，随着下一级的变化而自动变化。关于下级人数多少才能晋升，数字可以在后台设置）
5.	分红方案：每季度对管理层给予一次分红。分红比例按照主管、经理、高级经理分别为50/30/20比例。  分红计算方式为：每位主管分红金额=分红金额*50%/总分红人数。每位经理分红金额=分红金额*30%/（高级经理人数+经理人数）+主管级分红金额。每位高级经理分红金额=分红金额*20%/高级经理人数+经理级分红金额。(每次分红金额可以人为控制）
6.	每位贵宾会员消费，都会从其实际支付的消费金额中提取1%的比例记录到会员的上一级收益中，并累计到其收益记录中，收益累计100元以上可以提现，提现可以有一定的手续费，此收益也可以用于超市消费。（实现这些需要跟超市系统做好接口）
7.	职级以及贵宾会员限定权益：每年继续购买指定产品方可继续享受超市的优惠以及职级权益，按照实际购买产品的贵宾会员人数，贵宾会员可以继续享受原来的提成方案；下一年度如果不在续费购买超市指定的商品，自动取消贵宾会员及职级资格，同时不再享受原本可以享受的提成和分红权益。
8.	股东人数变更：动态的管理，受第二年续费的影响。
9.	职级晋升：①贵宾会员推荐产品达到20套（直推），即可成为业务主管；业务主管下面产生10名业务主管（10名主管不在一条线上），即可升级为经理；经理下面产生6名经理即可升级为高级经理（6名经理不在一条线上）；（职级的身份是动态的，随着下一级的变化而自动变化。关于下级人数多少才能晋升，数字可以在后台设置）
10.	当发展的一个会员是使用老年机（老年人不会使用智能手机）的时候，可以使用同一部手机注册，即APP可以内部可以切换账号
11.	刚发展的会员（我们通常成为的普通会员）没有会员称号，只有一个账号可以在后台用来查询到这个会员的信息（用来发展成贵宾会员），只有购买指定的产品之后成为贵宾的会员才对接到超市系统中
二.	App功能要求

1.	注册：
下载APP，提示注册新会员，填写注册信息和分享码，即可成为普通会员，生成普通会员号，资料自动导入到超市系统的普通会员档案信息里面；购买指定产品即可成为贵宾会员，生成新的贵宾会员号，资料自动导入到超市系统的贵宾会员档案信息里面。购买指定商品有10种左右产品备选，可以手机移动支付，（会员仅仅注册，没有购买指定的产品，不享受优惠，只是普通会员，所有商品只能原价购买）。
2.	贵宾会员指定购买商品取货方式：
①在线购买，需要送货上门（需有收货地址），贵宾会员的商品发出后，后台认为做出货已发出标记，并记录发货人及发货时间。
②在线购买，实体店自取，取货后系统标准货已发出并记录发货人。
3.	报表查询内容:
①提成累计数 ②已提现数 ③直推会员数 ④会员消费总金额 ⑤会员消费折扣折让总金额（其中会员消费总金额、折扣折让总金额需要从超市收银软件中提取报表）。
4.	商城购物流程：商城分为多家门店。每家门店都可以后台标注经纬度，打开APP，门店排序可以做到由近到远排
5.	序，消费者根据需要选择合适的门店选择商品。为先选择门店然后商品再下单。下单后消费者可以选择送货地址，送货地址为超市自行设置收货驿站，消费者只能到指定的收货驿站取货，收货驿站跟指定门店挂接在一起；一个收货驿站允许挂接到多个门店上面，当消费者下单后，如选择送货，即提示选择收货驿站，系统会弹出该门店收货驿站的列表共选择。 
6.	商家入驻功能 填写上传商家信息，由后台审核再联系商家（商家要有上传商品的功能）。
三.	App后台要求
1.	会员制度设置：发展直推会员奖励设置；第二层会员给第一层提成设置；第三层给第一、第二层提成设置。
2.	各项报表查询：
①会员总数、贵宾会员数，各职级数、每日新增会员数，每日新增贵宾会员数、每个贵宾会员下面直推贵宾会员数。每个贵宾会员下三层内贵宾会员数；各职级数及每个职级的下一级职级数。
②每日入会总金额、累计入会总金额、支付提成金额、会员提现金额、沉淀金额、待提现金额、沉淀金额减去待提现金额的余额、手续费金额等。
③会员销售费零售总金额、折扣折让总金额、实际消费支付金额：每个会员消费零售总金额、折扣折让总金额、实际支付金额。
3.	权限分配：设置操作权限、查询操作权限决策操作权限。
4.	与超市进销存接口：提取超市会员消费报表，包括：每日消费零售金额、消费会员折扣折让金额、实际支付金额；把会员通过APP注册的会员资料带入到超市系统中
5.	

测试时间： 确定UI界面后，预计6周（限工作日）后，提供项目测试地址。




