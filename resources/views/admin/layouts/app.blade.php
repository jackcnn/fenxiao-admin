<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8">
    <title>朋友淘-后台管理</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 3.3.4 -->
    <link href="/admin_resource/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
     <link href="/admin_resource/dist/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/admin_resource/dist/css/ionicons.min.css" rel="stylesheet" type="text/css" />
   <!-- Theme style -->
    <link href="/admin_resource/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="/admin_resource/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />

    <script src="/admin_resource/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="/admin_resource/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>


    <script src="/admin_resource/plugins/underscore-min.js" type="text/javascript"></script>
    <script src="/admin_resource/plugins/layer-v3.1.1/layer.js" type="text/javascript"></script>

    <!-- AdminLTE App -->
    <script src="/admin_resource/dist/js/app.js" type="text/javascript"></script>

    <script type="text/javascript">
      $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });                
      });

      function setNav(sec){
        $("#menu "+sec).addClass('active').parents(".treeview").addClass("active");
      }

      //dataTable 国际化
      var oLanguage={
          "oAria": {
              "sSortAscending": ": 升序排列",
              "sSortDescending": ": 降序排列"
          },
          "oPaginate": {
              "sFirst": "首页",
              "sLast": "末页",
              "sNext": "下一页",
              "sPrevious": "上一页"
          },
          "sEmptyTable": "没有相关记录",
          "sInfo": "第 _START_ 到 _END_ 条，共 _TOTAL_ 条",
          "sInfoEmpty": "共 0 条",
          "sInfoFiltered": "(从 _MAX_ 条记录中检索)",
          "sInfoPostFix": "",
          "sDecimal": "",
          "sThousands": ",",
          "sLengthMenu": "每页显示条数: _MENU_",
          "sLoadingRecords": "正在载入...",
          "sProcessing": "正在载入...",
          "sSearch": "搜索:",
          "sSearchPlaceholder": "",
          "sUrl": "",
          "sZeroRecords": "没有相关记录"
      };

      window.g_data = [];
      var search_param = [];

      //dataTable 请求函数
      function dataTable_fnServerData( sSource, aoData, fnCallback ) {
          aoData = $.merge(aoData, search_param);

          var loading = layer.load(1, {
              shade: [0.3,'#000']
          });

          $.ajax( {
            "dataType": 'json',
            "type": "GET",
            "url": sSource,
            "data": aoData,
            "success": fnCallback,
            "complete": function(data){
              var data = data.responseJSON.data;
              layer.close(loading);
              $.each(data, function(){
                g_data[this.id] = this;
              });
            }
          });
      }
      
      //dataTable 默认设置
      var dataTable_param = {
          "bPaginate": true,
          "bLengthChange": false,
          'bProcessing': true,
          "bFilter": false,
          "bSort": false,
          "bInfo": true,
          "bAutoWidth": false,
          "iDisplayLength": 20,
          "bServerSide": true,
          'bStateSave':true,
          "sAjaxDataProp":'data',
          "sPaginationType": 'full_numbers',
          "oLanguage":oLanguage,
          "fnServerData": dataTable_fnServerData        
      };

    </script>

  </head>

  <body class="skin-blue sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">淘</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">朋友淘</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <a href="/logout" style="color: #ffffff; display: block; height: 50px;line-height: 50px;padding: 0 20px;">注销登录</a>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar Menu -->
          <ul class="sidebar-menu" id="menu">
            <li class="header"></li>
            <li class="dashboard"><a href="{{route("admin.dashboard")}}"><i class='fa fa-dashboard'></i> <span>首页</span></a></li>
            <li class="member"><a href="{{route("admin.user")}}"><i class='fa fa-users'></i> <span>用户管理</span></a></li>
            <li class="order"><a href="{{route("admin.orderlist")}}"><i class='fa fa-bars'></i> <span>订单管理</span></a></li>
            <li class="feedback"><a href="{{route("admin.feedback")}}"><i class='fa fa-bars'></i> <span>意见反馈</span></a></li>
            <li class="banner"><a href="{{route("admin.banner")}}"><i class='fa fa-bars'></i><span>Banner管理</span></a></li>
            <li class="user_bill"><a href="{{route("admin.user_bill")}}"><i class='fa fa-bars'></i> <span>账单管理</span></a></li>
            <li class="notice"><a href="{{route("admin.notice")}}"><i class='fa fa-bars'></i> <span>公告管理</span></a></li>
            <li class="withdraw"><a href="{{route("admin.withdraw")}}"><i class='fa fa-bars'></i> <span>提现申请</span></a></li>
            <li class="message"><a href="{{route("admin.message")}}"><i class='fa fa-bars'></i> <span>通知管理</span></a></li>
            <li class="apply_upgrade"><a href="{{route("admin.apply_upgrade")}}"><i class='fa fa-bars'></i> <span>直升等级申请</span></a></li>
            <li class="apply_guaji"><a href="{{route("admin.apply_guaji")}}"><i class='fa fa-bars'></i> <span>挂机续费申请</span></a></li>

            <li class="treeview">
              <a href="#"><i class='fa fa-cog'></i> <span>系统设置</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="pid"><a href="{{route("admin.pid")}}">PID管理</a></li>
                <li class="taobaotoken"><a href="{{route("admin.taobaotoken")}}">系统授权管理</a></li>
                <li class="system_config"><a href="{{route("admin.system_config")}}">系统配置</a></li>
                <li class="wechat_domain"><a href="{{route("admin.wechat_domain")}}">域名管理</a></li>
                <li class="grade"><a href="{{route("admin.grade")}}">等级管理</a></li>
                <li class="failed_jobs"><a href="{{route("admin.failed_jobs")}}">失败任务</a></li>
                <li class="updatepwd"><a href="{{route("admin.system.updatepwd")}}">修改密码</a></li>
              </ul>
            </li>

          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
            @yield('content')
      </div><!-- /.content-wrapper -->

      
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class='control-sidebar-bg'></div>
    </div><!-- ./wrapper -->

    <style type="text/css">
    .dataTables_paginate{
      float: right;
    }
    .pagination {
        margin: 0;
    }
    .main-header{
		position: fixed;
		width: 100%; 
    }
    .main-sidebar{
		position: fixed;    	
    }
    .content-wrapper{
      padding-top: 50px;
    }
    .edui-scale{
        -moz-box-sizing: content-box;        
        box-sizing: content-box;
    }
    </style>
  </body>
</html>
