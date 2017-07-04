<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
$regionIds = array("cn-hongkong", "cn-hangzhou","cn-beijing","cn-qingdao","cn-shanghai","us-west-1","cn-shenzhen","ap-southeast-1");
$productDomains =array(
	new DM_Regions_ProductDomain("Mts", "mts.cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("ROS", "ros.aliyuncs.com"),
	new DM_Regions_ProductDomain("Dm", "dm.aliyuncs.com"),
	new DM_Regions_ProductDomain("Bss", "bss.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ecs", "ecs.aliyuncs.com"),
	new DM_Regions_ProductDomain("Oms", "oms.aliyuncs.com"),
	new DM_Regions_ProductDomain("Rds", "rds.aliyuncs.com"),
	new DM_Regions_ProductDomain("BatchCompute", "batchCompute.aliyuncs.com"),
	new DM_Regions_ProductDomain("Slb", "slb.aliyuncs.com"),
	new DM_Regions_ProductDomain("Oss", "oss-cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("OssAdmin", "oss-admin.aliyuncs.com"),
	new DM_Regions_ProductDomain("Sts", "sts.aliyuncs.com"),
	new DM_Regions_ProductDomain("Push", "cloudpush.aliyuncs.com"),
	new DM_Regions_ProductDomain("Yundun", "yundun-cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("Risk", "risk-cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("Drds", "drds.aliyuncs.com"),
	new DM_Regions_ProductDomain("M-kvstore", "m-kvstore.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ram", "ram.aliyuncs.com"),
	new DM_Regions_ProductDomain("Cms", "metrics.aliyuncs.com"),
	new DM_Regions_ProductDomain("Crm", "crm-cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ocs", "pop-ocs.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ots", "ots-pop.aliyuncs.com"),
	new DM_Regions_ProductDomain("Dqs", "dqs.aliyuncs.com"),
	new DM_Regions_ProductDomain("Location", "location.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ubsms", "ubsms.aliyuncs.com"),
	new DM_Regions_ProductDomain("Drc", "drc.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ons", "ons.aliyuncs.com"),
	new DM_Regions_ProductDomain("Aas", "aas.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ace", "ace.cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("Dts", "dts.aliyuncs.com"),
	new DM_Regions_ProductDomain("R-kvstore", "r-kvstore-cn-hangzhou.aliyuncs.com"),
	new DM_Regions_ProductDomain("PTS", "pts.aliyuncs.com"),
	new DM_Regions_ProductDomain("Alert", "alert.aliyuncs.com"),
	new DM_Regions_ProductDomain("Push", "cloudpush.aliyuncs.com"),
	new DM_Regions_ProductDomain("Emr", "emr.aliyuncs.com"),
	new DM_Regions_ProductDomain("Cdn", "cdn.aliyuncs.com"),
	new DM_Regions_ProductDomain("COS", "cos.aliyuncs.com"),
	new DM_Regions_ProductDomain("CF", "cf.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ess", "ess.aliyuncs.com"),
	new DM_Regions_ProductDomain("Ubsms-inner", "ubsms-inner.aliyuncs.com"),
    new DM_Regions_ProductDomain("Green", "green.aliyuncs.com")

	);
$endpoint = new DM_Regions_Endpoint("cn-hongkong", $regionIds, $productDomains);
$endpoints = array($endpoint);
DM_Regions_EndpointProvider::setEndpoints($endpoints);