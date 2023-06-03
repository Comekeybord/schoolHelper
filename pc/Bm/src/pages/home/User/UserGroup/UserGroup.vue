<script setup>
import { checkGroupNameStrict } from "@/model/formRules";
import { isNumber, toNumber } from "lodash";
import { onMounted, reactive, getCurrentInstance, ref, watch } from "vue";
import loding from "@/hooks/useLoding";
import { ElMessage, ElMessageBox } from "element-plus";
const { proxy } = getCurrentInstance();

onMounted(() => {
  // 获取用户组数据
  getUserGroup();
  dialogConfig.dialogVisible = false;
});

// 定义搜索数据
const searchInput = reactive({
  keyword: "",
});

// 定义表格数据
const groupList = ref([]);

// 定义表格数据，因为搜索要替换
const tableData = ref([]);

// 定义表格配置
const tableConfig = reactive([
  {
    label: "用户组ID",
    prop: "group_id",
  },
  {
    label: "用户组名",
    prop: "group_name",
  },
  {
    label: "用户组状态",
    prop: "status",
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

// 定义添加编辑用户组数据
const addGroupForm = reactive({
  group_id: 0,
  group_name: "",
  rights: [],
  status: "",
});

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

// 定义权限 id 对应对象
const authObj = reactive({});

// 获取用户组数据
const getUserGroup = async () => {
  loding.showLoading(".group-table");
  // 先获取菜单列表
  await getMenuList();
  const res = await proxy.$api.getUserGroup({ limit: navConfig.limit });
  // console.log(res);
  const { data } = res;
  // 计算总用户组数
  let sum = 0;
  let dataList = [];
  dataList = data.map((page) => {
    sum += page.length;

    // 处理用户权限，id数组转换成对应权限
    return page.map((group) => {
      group.rights = group.rights.map((authId) => authObj[authId]);
      return group;
    });
  });
  navConfig.total = sum;

  groupList.value = dataList;
  tableData.value = dataList;
};
// 获取菜单列表
const getMenuList = async () => {
  const res = await proxy.$api.getMenuList({});
  if (res.code !== 200) {
    ElMessage({
      message: "菜单列表请求失败,请刷新页面重试!",
      type: "error",
    });
    return;
  }
  menuList.value = res.data;
  // 给权限obj赋值
  res.data.forEach((menu) => {
    authObj[menu.mid] = menu.label;
  });
};
// 获取到数据关闭加载框
watch(groupList, (newList) => {
  if (!!newList) {
    loding.hideLoading();
  }
});

// 获取行的类名
const groupTableRowClassName = ({ row }) => {
  // console.log(row);
  if (row.status == "关闭") {
    return "close";
  }
};

// 翻页
const currentChange = (page) => {
  navConfig.currentChange = page;
};

// 搜索功能
const handleSearch = () => {
  const keyword = searchInput.keyword;
  let tmpList = [];
  // 从所有用户组的组名字段中查找关键字
  groupList.value.forEach((page) => {
    // console.log(page);
    tmpList = tmpList.concat(
      page.filter((group) => {
        if (group["group_name"].indexOf(keyword) !== -1) return true;
      })
    );
  });

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
  // 计算页数，向上取整
  let resList = [];
  const pages = Math.ceil(tmpList.length / navConfig.limit);
  for (var i = 0; i < pages; i++) {
    // 分页
    resList.push(tmpList.slice(i * navConfig.limit, (i + 1) * navConfig.limit));
  }
  // console.log(resList);
  // 把结果赋给用户组列表
  tableData.value = resList;
};

// 删除用户组
const groupDelete = async (row) => {
  ElMessageBox.confirm(`确定删除用户组 "${row.group_name}" 吗?`, "警告", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  }).then(async () => {
    // 确定删除
    const res = await proxy.$api.groupDel({ group_id: row.group_id });
    // console.log(res);
    if (res.code == 200) {
      await ElMessageBox.alert(res.msg, "提示", {
        confirmButtonText: "确定",
        type: "success",
      });

      // 重新获取用户组列表
      getUserGroup();
    }
  });
};

// 关闭添加用户组对话框时提示
const dialogClose = (done, updateUserList = false) => {
  ElMessageBox.confirm("确定关闭对话框?", "警告", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  }).then(async () => {
    dialogConfig.dialogVisible = false;
    // 清除表单
    await proxy.$refs.addGroupRef.resetFields();
    if (updateUserList) getUserGroup();
  });
};

// 取消编辑用户组
const userEditCancel = () => {
  proxy.$refs.addGroupRef.resetFields();
  dialogConfig.dialogVisible = false;
};

// 编辑用户组
const groupEdit = (row) => {
  // console.log(row);
  dialogConfig.dialogTitle = "编辑用户组";
  dialogConfig.dialogVisible = true;
  Object.assign(addGroupForm, row);
};

// 添加用户组
const GroupAdd = () => {
  dialogConfig.dialogTitle = "添加用户组";
  dialogConfig.dialogVisible = true;
};

// 处理并提交表单
const groupSubmit = async () => {
  // 验证表单
  const next = await proxy.$refs.addGroupRef.validate((validate) => {
    if (!validate) return false;
    return true;
  });
  if (!next) {
    ElMessage({
      type: "warning",
      message: "请先完整填写表单",
    });
    return;
  }
  // console.log("groupSubmit");
  // 将rights转为对应的权限id
  const turnRights = !!addGroupForm.rights;
  const tmpRightsList = addGroupForm.rights;
  if (turnRights) {
    addGroupForm.rights = addGroupForm.rights.map((auth) => {
      for (var k in authObj) {
        if (auth == authObj[k]) {
          return k;
        }
      }
    });
  }
  // 将状态转换为 字符串
  if (isNumber(addGroupForm.status)) {
    // console.log("status is number");
    addGroupForm.status = groupStatus[addGroupForm.status];
  }

  // 请求
  const res = await proxy.$api.addGroup(addGroupForm);
  // console.log(res);
  const { data } = res;
  if (res.code !== 200) {
    ElMessageBox.alert(res.msg, "提示", {
      confirmButtonText: "确定",
      type: "warning",
    });
  }
  ElMessageBox.alert(res.msg, "提示", {
    confirmButtonText: "确定",
    type: "success",
  });

  // 请求完成
  // 还原表单数据
  if (turnRights) {
    addGroupForm.rights = tmpRightsList;
  }
};
</script>

<template>
  <!-- 用户组管理页头部，添加 搜索 -->
  <div class="user_header">
    <el-button type="primary" @click="GroupAdd">+添加用户组</el-button>
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

  <!-- 用户组表格 -->
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
      <el-table-column
        class="group-rights"
        label="用户组权限(鼠标经过显示)"
        prop="rights"
      >
        <template #default="scope">
          <el-popover
            title="用户组权限列表"
            style="text-align: center"
            width="188"
            trigger="hover"
            placement="top"
          >
            <template #default>
              <el-tag
                style="margin: 3px"
                v-for="(item, index) in scope.row.rights"
                >{{ item }}</el-tag
              >
            </template>
            <template #reference>
              <el-tag>{{ scope.row.rights.length }}个权限</el-tag>
            </template>
          </el-popover>
        </template>
      </el-table-column>
      <el-table-column
        min-width="88"
        fixed="right"
        prop="userEdit"
        label="用户组操作"
      >
        <template #default="scope">
          <el-button type="primary" size="small" @click="groupEdit(scope.row)"
            >编辑</el-button
          >
          <el-button type="danger" size="small" @click="groupDelete(scope.row)"
            >删除</el-button
          >
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

  <!-- 添加用户组对话框 -->
  <el-dialog
    v-model="dialogConfig.dialogVisible"
    :title="dialogConfig.dialogTitle"
    width="30%"
    :before-close="dialogClose"
    draggable
    modal
  >
    <!-- 表单 -->
    <!-- 通过label-position="right"控制label的位置为靠右对齐 -->
    <!-- el-form-item必须加上prop属性才能使用form的方法 -->
    <!-- model的值与ref的值不能一样，否则表单无法输入 -->
    <!-- 通过rules数字验证每个from-item项，也可以声明一个大的数组在el-form中填写 -->
    <el-form
      :model="addGroupForm"
      label-position="right"
      label-width="auto"
      style="max-width: 460px"
      ref="addGroupRef"
      :rules="rules"
    >
      <!-- 给id占个位，区分添加还是编辑 -->
      <el-form-item label="用户组ID:" prop="group_id" v-show="false">
        <el-input v-model.trim="addGroupForm.group_id" />
      </el-form-item>
      <!-- 用户组名 -->
      <el-form-item label="用户组名:" prop="group_name">
        <el-input
          v-model.trim="addGroupForm.group_name"
          autocomplete="off"
          placeholder="请输入用户组名"
        />
      </el-form-item>
      <!-- 用户组状态 -->
      <el-form-item label="用户组状态:" prop="status">
        <el-select v-model="addGroupForm.status" placeholder="请选择用户组状态">
          <el-option
            v-for="(value, key, index) in groupStatus"
            :label="value"
            :value="toNumber(key)"
          />
        </el-select>
      </el-form-item>
      <!-- 用户组权限 -->
      <el-form-item label="用户组权限:" prop="rights">
        <el-checkbox-group v-model="addGroupForm.rights">
          <el-checkbox v-for="(item, index) in menuList" :label="item.label" />
        </el-checkbox-group>
      </el-form-item>
    </el-form>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="userEditCancel">取消</el-button>
        <el-button type="primary" @click="groupSubmit"> 提交 </el-button>
      </span>
    </template>
  </el-dialog>
</template>

<style lang="less" scoped>
.user_header {
  display: flex;
  justify-content: space-between;

  .search-form {
    display: flex;
    justify-content: flex-end;
  }
}

// 根据用户状态显示行颜色
:deep(.close) {
  --el-table-tr-bg-color: var(--el-color-warning-light-8);
}

:deep(.el-card__body) {
  height: 100%;
}
// 权限列样式
// :deep(tr td:nth-child(4) .cell) {
//   display: flex;
//   flex-direction: column;
//   flex-wrap: wrap;
//   align-content: space-around;
//   justify-content: space-around;
//   align-items: stretch;
// }
</style>
