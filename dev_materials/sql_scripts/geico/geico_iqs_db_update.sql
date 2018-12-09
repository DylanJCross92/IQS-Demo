/** iqsversion="1.8.11_dev_20170409_135003" **/
-- noinspection SqlNoDataSourceInspectionForFile
USE [IQS]
GO


-- v1.8.9 add method credentials override values

 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'methodovrduid_ezaddresses', N'com.sagesure.apps.ezquote')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'methodovrdpw_ezaddresses', N'76a8bae2928fc36ca31f1257ab6d79e2')

GO
