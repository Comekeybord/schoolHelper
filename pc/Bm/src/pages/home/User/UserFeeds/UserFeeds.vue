<script setup>
import { checkGroupNameStrict } from "@/model/formRules";
import { filter, isNumber, toNumber, trim } from "lodash";
import { onMounted, reactive, getCurrentInstance, ref, watch } from "vue";
import loding from "@/hooks/useLoding";
import { ElMessage, ElMessageBox } from "element-plus";
const { proxy } = getCurrentInstance();

/*
  加载数据
*/
onMounted(() => {
  // 获取反馈数据
  getUserFeeds();
  dialogConfig.dialogVisible = false;
});

/*
  定义数据
*/
// #region
// 定义搜索数据
const searchInput = reactive({
  keyword: "",
});

// 定义表格数据
const feedList = ref([]);

// 定义表格数据，因为搜索要替换
const tableData = ref([]);

// 定义表格配置
/*
  feed: "你们能开通聊天模块吗"
  feed_time: "2022-10-10 8:50"
  fid: 1
  name: "yk123"
  uid: 1
*/
const tableConfig = reactive([
  {
    label: "反馈ID",
    prop: "fid",
  },
  {
    label: "用户ID",
    prop: "uid",
  },
  {
    label: "反馈时间",
    prop: "feed_time",
  },
]);

// 定义分页器配置
const navConfig = reactive({
  currentChange: 1,
  total: 0,
  limit: 10,
});

// 定义对话框配置
const dialogConfig = reactive({
  dialogVisible: true,
  dialogTitle: "",
});

// 添加菜单信息
const menuList = ref([]);

// 定义用户组状态
const groupStatus = reactive({
  1: "开启",
  0: "关闭",
});

// 定义表单规则
const rules = reactive({
  group_name: [
    {
      required: true,
      message: "必填项",
    },
    {
      validator: checkGroupNameStrict,
      message: "中文和数字组成",
      trigger: "blur",
    },
  ],
  status: [
    {
      required: true,
      message: "必填项",
    },
  ],
  rights: [
    {
      required: true,
      message: "必选项",
    },
  ],
});

// 定义筛选配置
const filterConfig = reactive({
  ok: false,
  no: false,
});

// 定义回复框显示
const drawer = ref(false);
// 定义回复框数据
const drawerTitle = ref("");
const drawerFeed = ref("");
const drawerBack = ref("");
const drawerFid = ref(0);
// #endregion

/*
  定义方法
*/
// #region
// 按照分页配置整理反馈列表
function getNavList(arr) {
  // console.log(arr instanceof Array);
  if (!(arr instanceof Array)) {
    return console.error("getNavList 函数只可以传递数组!");
  }
  if (arr.length === 0) {
    return console.warn("getNavList 参数数组为空数组!");
  }

  // 按limit划分数组
  let pages = Math.ceil(navConfig.total / navConfig.limit);
  let tmpArr = [];
  for (var i = 0; i < pages; i++) {
    tmpArr.push(arr.slice(i * navConfig.limit, (i + 1) * navConfig.limit));
  }
  // console.log(tmpArr);
  //
  return tmpArr;
}
// 获取反馈信息
async function getUserFeeds() {
  loding.showLoading(".user-card");
  const res = await proxy.$api.getUserFeeds({});
  // console.log(res);
  if (res.code !== 200) {
    ElMessage({
      message: "用户反馈列表获取失败,请刷新页面重试!",
      type: "error",
    });
  }

  // 设置nav配置
  navConfig.total = res.data.length;
  feedList.value = getNavList(res.data);
  tableData.value = getNavList(res.data);
  loding.hideLoading();
}
// 筛选反馈状态
function feedFilter() {
  if (!filterConfig.no && !filterConfig.ok) return;
  // console.log("================");
  let data = tableData.value;
  let resList = [];
  let total = 0;

  data.forEach((rowArray) => {
    rowArray.forEach((row) => {
      if (
        (row.feed_status && filterConfig.ok) ||
        (!row.feed_status && filterConfig.no)
      ) {
        total++;
        resList.push(row);
      }
    });
  });

  if (resList.length == 0) {
    ElMessage({
      message: "筛选结果为空!",
    });
    return;
  }
  // 将筛选结果分页
  // console.log(resList);
  navConfig.total = total;
  resList = getNavList(resList);
  // console.log(resList);
  // console.log("筛选完成");
  tableData.value = resList;
}
// 重置筛选
function filterReset() {
  tableData.value = feedList.value;
  let total = 0;
  for (var i = 0; i < feedList.value.length; i++) {
    total += feedList.value[i].length;
  }
  navConfig.total = total;
  filterConfig.no = filterConfig.ok = false;
}
// 搜索功能
const handleSearch = () => {
  const keyword = trim(searchInput.keyword);
  let tmpList = [];
  // 从所有用户组的组名字段中查找关键字
  feedList.value.forEach((page) => {
    // console.log(page);
    tmpList = tmpList.concat(
      page.filter((feed) => {
        if (feed["feed"].indexOf(keyword) !== -1) return true;
      })
    );
  });

  // console.log(tmpList);

  // console.log(tmpList);
  // 如果为空直接返回
  if (tmpList == 0) {
    ElMessage({
      type: "warning",
      message: "无搜索结果",
    });
    return;
  }

  navConfig.total = tmpList.length;
  // 根据limit分页
  let resList = [];
  resList = getNavList(tmpList);
  console.log(resList);
  tableData.value = resList;
};

// 回复消息
function feedBack(row) {
  // console.log(row.feed);
  drawerFid.value = row.fid;
  drawerTitle.value = `回复用户ID为 "${row.uid}" 的用户`;
  drawerFeed.value = row.feed;
  drawer.value = true;
}
// 关闭回复框
function drawerClose() {
  ElMessageBox.confirm("取消回复会丢失已编辑内容!", "警告", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  }).then(() => {
    drawer.value = false;
    drawerBack.value = "";
  });
}
// 提交回复
async function confirmDrawer() {
  if (trim(drawerBack.value) == "") {
    ElMessage({
      message: "请输入回复内容!",
      type: "warning",
    });
    return;
  }
  loding.showLoading(".drawer");
  const res = await proxy.$api.backUserFeed({
    fid: drawerFid.value,
    content: trim(drawerBack.value),
  });

  if (res.code !== 200) {
    ElMessage({
      message: "回复失败，请重试！",
      type: "warning",
    });
    return;
  }

  loding.hideLoading();

  ElMessage({
    message: "回复成功！",
    type: "success",
  });

  // 更新数据
  getUserFeeds();

  drawer.value = false;
  drawerBack.value = "";
}

// 删除回复
async function feedDelete(row) {
  ElMessageBox.confirm("确定要删除反馈吗?", "警告", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  }).then(async () => {
    const res = await proxy.$api.delUserFeed({ fid: row.fid });
    if (res.code !== 200) {
      ElMessage({
        message: "删除失败，请重试!",
        type: "error",
      });
      return;
    }
    ElMessage({
      message: "删除成功!",
      type: "success",
    });
  });
}
// #endregion
</script>

<template>
  <!-- 用户反馈页头部， 搜索 -->
  <div class="feed_header">
    <!-- 搜索框 -->
    <el-form :inline="true" :model="searchInput" class="search-form">
      <el-form-item label="请输入关键字">
        <el-input v-model="searchInput.keyword" placeholder="请输入关键字" />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="handleSearch">搜索</el-button>
      </el-form-item>
    </el-form>
  </div>
  <!-- 反馈列表 -->
  <el-card style="height: 85%" class="user-card">
    <el-table
      :data="tableData[navConfig.currentChange - 1]"
      height="100%"
      style="width: 100%; text-align: center"
      class="group-table"
      :header-cell-style="{ 'text-align': 'center' }"
      :cell-style="{ 'text-align': 'center' }"
      highlight-current-row="true"
      :row-class-name="groupTableRowClassName"
      empty-text="无数据"
    >
      <el-table-column
        v-for="(item, index) in tableConfig"
        :key="index"
        :prop="item.prop"
        :label="item.label"
        :width="item.width"
        :fixed="item.fixed"
      />
      <!-- 反馈内容 -->
      <el-table-column label="反馈内容(点击显示)" prop="feed">
        <template #default="scope">
          <el-popover trigger="click" width="200">
            <template #reference>
              <el-text
                style="cursor: pointer"
                truncated
                :type="scope.row.feed_status == 0 ? 'warning' : 'success'"
              >
                {{ scope.row.feed }}
              </el-text>
            </template>
            <el-text
              style="cursor: pointer"
              :type="scope.row.feed_status == 0 ? 'warning' : 'success'"
            >
              {{ scope.row.feed }}
            </el-text>
          </el-popover>
        </template>
      </el-table-column>
      <!-- 反馈状态 -->
      <el-table-column label="反馈状态" prop="feed_status">
        <template #header>
          <el-popover
            width="200"
            placement="bottom"
            trigger="click"
            popper-class="filter-table-input"
            title="反馈状态"
          >
            <template #reference>
              <span class="el-popover-link">
                反馈状态
                <el-icon class="el-icon--right">
                  <arrow-down />
                </el-icon>
              </span>
            </template>
            <div class="filter-box">
              <div>
                <el-checkbox v-model="filterConfig.ok" label="已办" />

                <el-checkbox v-model="filterConfig.no" label="待办" />
              </div>
              <div>
                <el-button
                  @click="feedFilter"
                  :disabled="!filterConfig.ok && !filterConfig.no"
                  >筛选</el-button
                >
                <el-button @click="filterReset">重置</el-button>
              </div>
            </div>
          </el-popover>
        </template>
        <template #default="scope">
          <el-tag :type="scope.row.feed_status === 0 ? 'primary' : 'success'">
            {{ scope.row.feed_status == 0 ? "待处理" : "已处理" }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column
        min-width="88"
        fixed="right"
        prop="feedEdit"
        label="用户反馈操作"
      >
        <template #default="scope">
          <el-button type="primary" size="small" @click="feedBack(scope.row)"
            >回复
          </el-button>
          <el-button type="danger" size="small" @click="feedDelete(scope.row)"
            >删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
  <el-row style="display: flex; justify-content: center; margin-top: 8px">
    <el-pagination
      background
      layout="prev, pager, next"
      :total="navConfig.total"
      @current-change="currentChange"
      hide-on-single-page="true"
      :page-size="navConfig.limit"
    />
  </el-row>

  <!-- 右侧回复框 -->
  <el-drawer
    class="drawer"
    direction="rtl"
    v-model="drawer"
    :close-on-click-modal="false"
    :lock-scroll="true"
  >
    <template #header>
      <h4>{{ drawerTitle }}</h4>
    </template>
    <template #default>
      <el-card>
        {{ drawerFeed }}
      </el-card>
    </template>
    <template #footer>
      <el-card style="margin-bottom: 20px">
        <el-input
          v-model="drawerBack"
          :autosize="{ minRows: 4, maxRows: 8 }"
          type="textarea"
          placeholder="请输入回复内容"
        />
      </el-card>
      <div style="flex: auto">
        <el-button @click="drawerClose">取消</el-button>
        <el-button type="primary" @click="confirmDrawer">确定</el-button>
      </div>
    </template>
  </el-drawer>
</template>

<style lang="less" scoped>
.feed_header {
  display: flex;
  justify-content: end;
}

:deep(.el-card__body) {
  height: 100%;
}

:deep(.el-popover-link) {
  cursor: pointer;
}

:deep(.filter-box) {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
