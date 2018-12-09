/** iqsversion="1.8.11_dev_20170409_135003" **/
USE [master]
GO
/****** Object:  Database [IQS]    Script Date: 11/05/2016 12:53:12 ******/
CREATE DATABASE [IQS] ON  PRIMARY
  ( NAME = N'IQS', FILENAME = N'C:\GIQ\db\IQS.mdf' , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
LOG ON
  ( NAME = N'IQS_log', FILENAME = N'C:\GIQ\db\IQS_log.ldf' , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [IQS] SET COMPATIBILITY_LEVEL = 100
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [IQS].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [IQS] SET ANSI_NULL_DEFAULT OFF
GO
ALTER DATABASE [IQS] SET ANSI_NULLS OFF
GO
ALTER DATABASE [IQS] SET ANSI_PADDING OFF
GO
ALTER DATABASE [IQS] SET ANSI_WARNINGS OFF
GO
ALTER DATABASE [IQS] SET ARITHABORT OFF
GO
ALTER DATABASE [IQS] SET AUTO_CLOSE OFF
GO
ALTER DATABASE [IQS] SET AUTO_SHRINK OFF
GO
ALTER DATABASE [IQS] SET AUTO_UPDATE_STATISTICS ON
GO
ALTER DATABASE [IQS] SET CURSOR_CLOSE_ON_COMMIT OFF
GO
ALTER DATABASE [IQS] SET CURSOR_DEFAULT  GLOBAL
GO
ALTER DATABASE [IQS] SET CONCAT_NULL_YIELDS_NULL OFF
GO
ALTER DATABASE [IQS] SET NUMERIC_ROUNDABORT OFF
GO
ALTER DATABASE [IQS] SET QUOTED_IDENTIFIER OFF
GO
ALTER DATABASE [IQS] SET RECURSIVE_TRIGGERS OFF
GO
ALTER DATABASE [IQS] SET  DISABLE_BROKER
GO
ALTER DATABASE [IQS] SET AUTO_UPDATE_STATISTICS_ASYNC OFF
GO
ALTER DATABASE [IQS] SET DATE_CORRELATION_OPTIMIZATION OFF
GO
ALTER DATABASE [IQS] SET TRUSTWORTHY OFF
GO
ALTER DATABASE [IQS] SET ALLOW_SNAPSHOT_ISOLATION OFF
GO
ALTER DATABASE [IQS] SET PARAMETERIZATION SIMPLE
GO
ALTER DATABASE [IQS] SET READ_COMMITTED_SNAPSHOT OFF
GO
ALTER DATABASE [IQS] SET HONOR_BROKER_PRIORITY OFF
GO
ALTER DATABASE [IQS] SET RECOVERY SIMPLE
GO
ALTER DATABASE [IQS] SET  MULTI_USER
GO
ALTER DATABASE [IQS] SET PAGE_VERIFY CHECKSUM
GO
ALTER DATABASE [IQS] SET DB_CHAINING OFF
GO
ALTER DATABASE [IQS] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF )
GO
ALTER DATABASE [IQS] SET TARGET_RECOVERY_TIME = 0 SECONDS
GO
USE [IQS]
GO
/****** Object:  Table [dbo].[ActiveSessions]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ActiveSessions](
	[ActiveSessionId] [int] IDENTITY(1,1) NOT NULL,
	[SessionGuid] [nvarchar](50) NOT NULL,
	[LastAccessDate] [datetime2](7) NOT NULL,
	[SessionData] [nvarchar](max) NULL,
 CONSTRAINT [PK_ActiveSessions] PRIMARY KEY CLUSTERED
(
	[ActiveSessionId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
/****** Object:  Table [dbo].[BlockCodes]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[BlockCodes](
	[BlockCode] [nvarchar](50) NOT NULL,
	[BlockText] [nvarchar](250) NULL,
	[DateTime] [datetime2](7) NULL,
 CONSTRAINT [PK_BlockCodes_BlockCode] PRIMARY KEY CLUSTERED
(
	[BlockCode] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[BlockedQuotes]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[BlockedQuotes](
	[BlockedQuoteId] [int] IDENTITY(1,1) NOT NULL,
	[QuoteId] [nvarchar](50) NOT NULL,
	[BlockCode] [nvarchar](50) NOT NULL,
 CONSTRAINT [PK_BlockedQuotes_1] PRIMARY KEY CLUSTERED
(
	[BlockedQuoteId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[Conf]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Conf](
	[ConfId] [int] IDENTITY(1,1) NOT NULL,
	[ConfSection] [nvarchar](50) NOT NULL,
	[ConfElement] [nvarchar](50) NOT NULL,
	[ConfValue] [nvarchar](500) NOT NULL,
 CONSTRAINT [PK_Conf] PRIMARY KEY CLUSTERED
(
	[ConfId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[DynConf]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[DynConf](
	[ConfId] [nvarchar](50) NOT NULL,
	[ConfValue] [nvarchar](500) NOT NULL,
 CONSTRAINT [PK_DynConf] PRIMARY KEY CLUSTERED
(
	[ConfId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[GeneratedQuotes]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[GeneratedQuotes](
	[GeneratedQuoteId] [int] IDENTITY(1,1) NOT NULL,
	[QuoteId] [nvarchar](50) NOT NULL,
	[CreateDate] [datetime2](7) NOT NULL,
	[LastAccessDate] [datetime2](7) NOT NULL,
	[CompleteDate] [datetime2](7) NULL,
	[AccessCount] [int] NOT NULL,
	[Feid] [nvarchar](50) NOT NULL,
	[SessionGuid] [nvarchar](50) NOT NULL,
	[LastSavedQuote] [nvarchar](max) NULL,
 CONSTRAINT [PK_GeneratedQuotes] PRIMARY KEY CLUSTERED
(
	[GeneratedQuoteId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
/****** Object:  Table [dbo].[GeneratedSessions]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[GeneratedSessions](
	[GeneratedSessionId] [int] IDENTITY(1,1) NOT NULL,
	[SessionGuid] [nvarchar](50) NOT NULL,
	[CreateDate] [datetime2](7) NOT NULL,
	[LastAccessDate] [datetime2](7) NOT NULL,
	[AccessCount] [int] NOT NULL,
	[Feid] [nvarchar](50) NOT NULL,
	[LastPageId] [nvarchar](50) NULL,
	[AltData] [nvarchar](50) NULL,
 CONSTRAINT [PK_GeneratedSessions] PRIMARY KEY CLUSTERED
(
	[GeneratedSessionId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[Log]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Log](
	[LogID] [int] IDENTITY(1,1) NOT NULL,
	[LogEntryCode] [nvarchar](50) NULL,
	[LogEntrySource] [nvarchar](1000) NULL,
	[LogEntryMessage] [nvarchar](1000) NULL,
	[LogEntryDateTime] [datetime] NULL,
 CONSTRAINT [PK_ErrorLog_1] PRIMARY KEY CLUSTERED
(
	[LogID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[SessionTracking]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[SessionTracking](
	[TrackingId] [int] IDENTITY(1,1) NOT NULL,
	[SessionGuid] [nvarchar](50) NOT NULL,
	[PageId] [nvarchar](50) NULL,
	[DateTime] [datetime2](7) NOT NULL,
 CONSTRAINT [PK_SessionTracking] PRIMARY KEY CLUSTERED
(
	[TrackingId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[ZipCodeWhiteList]    Script Date: 10/28/2016 9:32:05 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ZipCodeWhiteList](
	[WhiteListId] [int] IDENTITY(1,1) NOT NULL,
	[ZipCode] [nvarchar](10) NOT NULL,
	[State] [nvarchar](10) NOT NULL,
	[Product] [nvarchar](10) NOT NULL,
	[DateTime] [datetime2](7) NOT NULL,
 CONSTRAINT [PK_ZipCodeWhiteList] PRIMARY KEY CLUSTERED
(
	[WhiteListId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

/***** This section added dynamically in the build_giq.xml PHING script (for dev) or via other template scripts ****/

 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'logging', N'filelogging', N'true')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'logging', N'databaselogging', N'false')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'logging', N'logfilepath', N'/var/log/iqs/iqslog.log')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'baseurl', N'https://services.stage.sagesure.com/cru-4/ezquote/api/ezquote/2/')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'apiuid', N'g26100i')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'apipw', N'jGFiA2S7Az')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'apialc', N'g26100i')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'debugapiuid', N'giqtestuser')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'debugapipw', N'giqtesting2014')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'debugapialc', N'e80000t')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'methodovrduid_ezaddresses', N'com.sagesure.apps.ezquote')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'ezquoteapi', N'methodovrdpw_ezaddresses', N'76a8bae2928fc36ca31f1257ab6d79e2')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'statesenabled', N'fl', N'true')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'productsenabled', N'flho3', N'true')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'whitelistenabled', N'flho3', N'false')
 INSERT [dbo].[Conf] ([ConfSection], [ConfElement], [ConfValue]) VALUES (N'env', N'debug', N'false')

 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'110', N'location not covered all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'111', N'rental property unoccupied over 3 months all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'120', N'seasonal or secondary residence unoccupied over 3 months all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'121', N'location not covered', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'122', N'vacant property not a dp3 product all, except ny', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'123', N'full time student tenants all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'130', N'already has policy with occidental all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'210', N'current coverage a more than $2 million', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'211', N'more than 3 claims or has 2+ losses from the same peril all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'212', N'three family non-dp all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'213', N'four family non-dp all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'310', N'home built before 1900 all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'311', N'roof older than 50 years old all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'312', N'home has more than 3 additional features all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'313', N'in ground pool has no fence all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'314', N'in ground pool has a diving board or slide all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'315', N'above ground pool has a diving board or slide all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'316', N'above ground pool has an immovable ladder all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'317', N'dog on exclusion breed list all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'318', N'sq ft - over 10k all', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'319', N'wooden shingles tx', CURRENT_TIMESTAMP)
 INSERT [dbo].[BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (N'320', N'metal roof other than steel all', CURRENT_TIMESTAMP)


GO

USE [master]
GO
ALTER DATABASE [IQS] SET  READ_WRITE
GO
