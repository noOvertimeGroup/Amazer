<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <style type="text/css">
    /*.layui-table-view .layui-table[lay-size=lg] .layui-table-cell{height: auto !important;}*/
  </style>
  <?= $this->include('layouts/common/header', ['title' => '任务管理']) ?>
</head>
<body>
<table class="layui-hide" id="table" lay-filter="table"></table>
</body>

<script type="text/html" id="operation">
  <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="detail">查看</a>
  <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">取消</a>
</script>

<script type="module">
  import {getCookie} from '/service/js/util.js';
  import {task_delete} from '/service/js/api.js';
  import {postRequest} from '/service/js/request.js';

  layui.use(['table', 'code', 'util'], function () {
    var table = layui.table;
    var util = layui.util;
    table.render({
      elem: '#table'
      , url: '/task/list'
      , parseData: function (res) { //res 即为原始返回的数据
        return {
          "code": res.code, //解析接口状态
          "msg": res.message, //解析提示文本
          "count": res.data.total, //解析数据长度
          "data": res.data.data //解析数据列表
        };
      }
      , size: 'lg'
      , even: true
      , headers: {
        'Authorization': 'Bearer ' + getCookie("TOKEN_WHARF")
      }
      , defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
        title: '提示'
        , layEvent: 'LAYTABLE_TIPS'
        , icon: 'layui-icon-tips'
      }]
      , height: 'full'
      , page: { //支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
        layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
        , curr: 1 //设定初始在第 5 页
        , groups: 1 //只显示 1 个连续页码
        , first: false //不显示首页
        , last: false //不显示尾页

      }
      , cellMinHeight: 100
      , cols: [[
        {fixed: 'left', title: '操作', toolbar: '#operation', rowspan: 2, width: 120}
        , {field: 'taskId', minWidth: 80, title: 'taskId', sort: true, rowspan: 2}
        , {field: 'names', minWidth: 100, title: '任务名称', rowspan: 2}
        , {field: 'describe', minWidth: 100, title: '任务描述', rowspan: 2}
        , {
          field: 'execution', minWidth: 80, title: '执行时间', sort: true, rowspan: 2, templet: function (d) {
            return util.toDateString(d.execution * 1000);
          }
        }
        , {field: 'retry', minWidth: 80, title: '重试次数', sort: true, rowspan: 2}
        , {title: '请求体', align: 'center', minWidth: 80, colspan: 12}
        , {field: 'status', minWidth: 80, title: '状态', sort: true, rowspan: 2}
        , {
          field: 'createdAt', minWidth: 80, title: '创建时间', rowspan: 2, sort: true, templet: function (d) {
            return util.toDateString(d.createdAt * 1000);
          }
        }
        , {
          field: 'updatedAt', minWidth: 80, title: '更新时间', rowspan: 2, sort: true, templet: function (d) {
            return util.toDateString(d.updatedAt * 1000);
          }
        }
      ], [
        {field: 'body.url', title: "URL", templet: '<div>{{d.bodys.url}}</div>'}
        , {field: 'bodys.method', title: "Method", templet: '<div>{{d.bodys.method}}</div>'}
        , {field: 'bodys.cookies', title: "Cookie", templet: '<div>{{d.bodys.cookies??"没有传参"}}</div>'}
        , {field: 'bodys.body', title: "Body", templet: '<div>{{d.bodys.body??"没有传参"}}</div>'}
        , {field: 'bodys.headers', title: "Headers", templet: '<div>{{d.bodys.headers??"没有传参"}}</div>'}
        , {field: 'bodys.form_params', title: "FormParams", templet: '<div>{{d.bodys.form_params??"没有传参"}}</div>'}
        , {field: 'bodys.timeout', title: "Timeout", templet: '<div>{{d.bodys.timeout??"没有传参"}}</div>'}
        , {field: 'bodys.version', title: "Version", templet: '<div>{{d.bodys.version??"没有传参"}}</div>'}
        , {
          field: 'bodys.connect_timeout',
          title: "ConnectTimeout",
          templet: '<div>{{d.bodys.connect_timeout??"没有传参"}}</div>'
        }
        , {field: 'bodys.verify', title: "Verify", templet: '<div>{{d.bodys.verify??"没有传参"}}</div>'}
      ]]
    });

    //监听行工具事件
    table.on('tool(table)', function (obj) {
      var data = obj.data;
      if (obj.event === 'del') {
        layer.confirm('真的要取消任务吗？', function (index) {
          postRequest(task_delete, {'taskId': data.taskId}, function (result) {
            obj.update({
              status: "执行取消!(:<"
            });
          });
          layer.close(index);
        });
      } else if (obj.event === 'detail') {
        layer.msg('TASKID：' + data.taskId + ' 的查看操作');
      }
    });
  });
</script>
</html>