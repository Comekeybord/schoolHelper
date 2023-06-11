<script setup>
import { toNumber } from "lodash";
import { getCurrentInstance, onMounted, reactive, ref, watch } from "vue";
// 导入自定义hook 显示隐藏加载框
import loding from "@/hooks/useLoding";
// 导入自定义表单校验规则
import {
  checkPhoneStrict,
  checkQQStrict,
  checkPwdStrict,
  checkAccStrict,
} from "@/model/formRules";
import { ElMessage } from "element-plus";

const { proxy } = getCurrentInstance();

onMounted(() => {
  // 获取数据
  getUserList();
  getUserGroup();
  // 隐藏编辑用户对话框
  // 渲染完成再隐藏，为了解决表单重置的问题
  // 因为如果先点了编辑再挂载，那么初始值就会重置为编辑的用户的信息
  dialogConfig.dialogVisible = false;
});

// 表格分页配置
const navConfig = reactive({
  total: 0,
  page: 1,
  limit: 10,
});

// 定义用户数据列表
const userList = ref([]);

// 定义性别、用户组、状态对象对应关系
const sex = reactive({
  0: "男",
  1: "女",
});
const groupName = reactive({
  // 1: "超级管理员",
  // 2: "普通管理员",
  // 3: "普通用户",
});
const userStatus = reactive({
  1: "开启",
  0: "关闭",
});

// 监听表格获取数据，关闭加载框
watch(
  () => userList.value,
  (newval) => {
    // console.log(userList.value, "更新了");
    if (!!newval) {
      loding.hideLoading();
    }
  }
);

// 表格参数
const tableConfig = reactive([
  {
    prop: "uid",
    label: "ID",
  },
  {
    prop: "account",
    label: "用户账号",
  },
  {
    prop: "name",
    label: "用户名",
  },
  {
    prop: "sex",
    label: "性别",
  },
  {
    prop: "phone",
    label: "手机",
  },
  {
    prop: "qq",
    label: "QQ",
  },
  {
    prop: "group_name",
    label: "用户组",
  },
  {
    prop: "status",
    label: "用户状态",
  },
]);

// 定义搜索框数据
const formInline = reactive({
  keyword: "",
});

// 定义对话框配置
const dialogConfig = reactive({
  dialogVisible: true,
  dialogTitle: "",
});

// 定义添加修改用户的表单数据
const addUserForm = reactive({
  // 用户id，uid为0表示添加，不为0表示修改
  uid: 0,
  account: "",
  password: "",
  name: "",
  phone: "",
  qq: "",
  sex: "",
  group_name: "",
  status: "",
});

// 用户表格行类名,控制显示状态
const userTableRowClassName = ({ row }) => {
  if (row.status == "开启") return "live";
  if (row.status == "关闭") return "close";
};

// 请求用户数据
const getUserList = async () => {
  // 显示加载
  loding.showLoading(".user-card");
  const res = await proxy.$api.getUserList(navConfig);

  // console.log(res);

  const { data } = res;
  // console.log(data);

  navConfig.total = data.total;
  userList.value = data.userList;
};

// 请求用户组数据
const getUserGroup = async () => {
  const res = await proxy.$api.getUserGroup({ limit: 10 });
  // console.log(res);
  if (res.code !== 200) {
    ElMessage({
      message: "获取用户组失败,请刷新页面重试!",
      type: "error",
    });
    return;
  }

  const { data } = res;
  data.forEach((page) => {
    page.forEach((group) => {
      var tmpObj = {};
      tmpObj[group["group_id"]] = group["group_name"];
      Object.assign(groupName, tmpObj);
    });
  });
  // console.log(groupName);
};

// 翻页
const currentChange = (page) => {
  // console.log(page);
  // 请求对应页的数据
  navConfig.page = page;
  getUserList();
};

// 查找用户
const handleSearch = async () => {
  const reqData = {
    keyword: formInline.keyword,
    limit: navConfig.limit,
    page: navConfig.page,
  };
  const res = await proxy.$api.userSearch(reqData);
  if (res.code !== 200) {
    ElMessage({
      message: "无搜索结果!",
      type: "warning",
    });
    return;
  }
  const data = res.data;
  // console.log(res);
  // console.log(data);
  navConfig.total = data.total;
  userList.value = data.userList;
};

// 删除用户
const userDelete = async (scope) => {
  // console.log(scope.row.uid);
  const uid = scope.row.uid;
  // console.log(res.code);
  // 询问是否真的删除
  await ElMessageBox.confirm(`真的要删除用户${scope.row.name}吗?`, "提示", {
    confirmButtonText: "确认",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      // 请求删除
      const res = await proxy.$api.userDelete({ uid });
      if (res.code !== 200) {
        await ElMessageBox.alert(res.msg, "提示", {
          confirmButtonText: "确认",
          type: "warning",
        });
        return;
      }
      await ElMessageBox.alert(res.msg, "提示", {
        confirmButtonText: "确认",
        type: "success",
      });
    })
    .catch(() => {
      ElMessage({
        message: "取消删除",
        type: "warning",
        duration: 1500,
      });
    });

  // 重新请求用户数据
  getUserList();
};

// 打开添加用户对话框
const openUserAdd = () => {
  // 修改标题
  dialogConfig.dialogTitle = "添加用户";
  dialogConfig.dialogVisible = true;
};

// 根据编辑还是添加分别确定密码是否必须
const pwdRule = reactive([
  {
    required: false,
    message: "必填项",
  },
  {
    validator: checkPwdStrict,
    message: "必须包含大写字母，小写字母，数字，特殊符号中的三项",
    trigger: "blur",
  },
]);
// 根据编辑还是添加分别确定密码是否必须
watch(
  () => addUserForm.uid,
  (newval) => {
    // console.log(newval);
    pwdRule[0].required = toNumber(newval) == 0 ? true : false;
  },
  {
    deep: true,
    immediate: true,
  }
);

// 打开编辑用户对话框
const editUser = (row) => {
  // 修改标题
  dialogConfig.dialogTitle = "编辑用户";
  dialogConfig.dialogVisible = true;
  // console.log(row);

  // 将qq和phone转为数字类型
  row.qq = toNumber(row.qq);
  row.phone = toNumber(row.phone);
  // 为表单赋值
  Object.assign(addUserForm, row);
};

// 关闭添加用户对话框时提示
const dialogClose = (done, updateUserList = false) => {
  ElMessageBox.confirm("确定关闭对话框?", "警告", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  }).then(async () => {
    dialogConfig.dialogVisible = false;
    // 清除表单
    await proxy.$refs.addUserRef.resetFields();
    if (updateUserList) getUserList();
  });
};

// 取消编辑用户
const userEditCancel = () => {
  // 重置表单
  proxy.$refs.addUserRef.resetFields();
  // 关闭dialog
  dialogConfig.dialogVisible = false;
};

// 提交表单
const userSubmit = async () => {
  // 先表单验证
  const next = await proxy.$refs.addUserRef.validate((validated) => {
    if (!validated) {
      ElMessage({
        message: "请按要求填写信息!",
        type: "warning",
      });
      return false;
    }
    return true;
  });
  if (!next) return;
  // 判断是添加还是编辑,根据表单中隐藏的uid判断

  // 编辑用户
  // 可能会修改性别和管理组,将键值修改成对应的值
  // 修改sex
  for (var k in sex) {
    if (addUserForm.sex == k) {
      addUserForm.sex = sex[addUserForm.sex];
    }
  }
  // 修改group
  for (var k in groupName) {
    if (addUserForm.group_name == k) {
      addUserForm.group_name = groupName[addUserForm.group_name];
    }
  }
  // 修改status
  for (var k in userStatus) {
    if (addUserForm.status == k) {
      addUserForm.status = userStatus[addUserForm.status];
    }
  }

  const res = await proxy.$api.addUser(addUserForm);
  // console.log(res);
  if (res.code !== 200) {
    await ElMessageBox.alert(res.msg, "提示", {
      confirmButtonText: "确定",
      type: "warning",
    });
    return;
  }
  await ElMessageBox.alert(res.msg, "提示", {
    confirmButtonText: "确定",
    type: "success",
  });

  // 更新用户列表
  getUserList();
};
</script>

<template>
  <!-- 用户管理页头部，添加 搜索 -->
  <div class="user_header">
    <el-button type="primary" @click="openUserAdd">+添加用户</el-button>
    <!-- 搜索框 -->
    <el-form :inline="true" :model="formInline" class="search-form">
      <el-form-item label="请输入关键字">
        <el-input v-model="formInline.keyword" placeholder="请输入关键字" />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="handleSearch">搜索</el-button>
      </el-form-item>
    </el-form>
  </div>

  <!-- 用户列表 -->
  <el-card style="height: 85%" class="user-card">
    <el-table
      :data="userList"
      height="100%"
      style="width: 100%; text-align: center"
      class="user-table"
      :header-cell-style="{ 'text-align': 'center' }"
      :cell-style="{ 'text-align': 'center' }"
      highlight-current-row="true"
      :row-class-name="userTableRowClassName"
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
        min-width="88"
        fixed="right"
        prop="userEdit"
        label="用户操作"
      >
        <template #default="scope">
          <el-button type="primary" size="small" @click="editUser(scope.row)"
            >编辑</el-button
          >
          <el-button type="danger" size="small" @click="userDelete(scope)"
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
    />
  </el-row>

  <!-- 添加用户对话框 -->
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
      :model="addUserForm"
      label-position="right"
      label-width="82px"
      style="max-width: 460px"
      ref="addUserRef"
    >
      <!-- 给id占个位，区分添加还是编辑 -->
      <el-form-item label="ID:" prop="uid" v-show="false">
        <el-input v-model.trim="addUserForm.uid" />
      </el-form-item>
      <!-- 账号 -->
      <el-form-item
        label="账号:"
        prop="account"
        :rules="[
          {
            required: true,
            message: '必填项',
          },
          {
            validator: checkAccStrict,
            message: '4到16位（字母，数字，下划线，减号）',
            trigger: 'blur',
          },
        ]"
      >
        <el-input
          v-model.trim="addUserForm.account"
          autocomplete="off"
          placeholder="请输入账号"
        />
      </el-form-item>
      <!-- 密码 -->
      <el-form-item label="密码:" prop="password" :rules="pwdRule">
        <el-input
          type="password"
          v-model.trim="addUserForm.password"
          autocomplete="off"
          :placeholder="
            dialogConfig.dialogTitle == '添加用户'
              ? '请输入密码'
              : '不修改则留空'
          "
        />
      </el-form-item>
      <!-- 姓名 -->
      <el-form-item
        label="用户名:"
        prop="name"
        :rules="[
          {
            required: true,
            message: '必填项',
          },
          {
            validator: checkAccStrict,
            message: '4到16位（字母，数字，下划线，减号）',
            trigger: 'blur',
          },
        ]"
      >
        <el-input
          v-model.trim="addUserForm.name"
          autocomplete="off"
          placeholder="请输入用户名"
        />
      </el-form-item>
      <!-- 性别 -->
      <el-form-item
        label="性别:"
        prop="sex"
        :rules="[
          {
            required: true,
            message: '必选项',
          },
        ]"
      >
        <el-select v-model="addUserForm.sex" placeholder="请选择你的性别">
          <el-option
            v-for="(value, key, index) in sex"
            :label="value"
            :value="toNumber(key)"
          />
        </el-select>
      </el-form-item>
      <!-- 手机号 -->
      <el-form-item
        label="手机号:"
        prop="phone"
        :rules="[
          {
            required: true,
            message: '必填项',
          },
          {
            validator: checkPhoneStrict,
            message: '请输入正确的手机号',
            trigger: 'blur',
          },
        ]"
      >
        <el-input
          v-model.number="addUserForm.phone"
          autocomplete="off"
          placeholder="请输入手机号"
        />
      </el-form-item>
      <!-- QQ号 -->
      <el-form-item
        label="QQ:"
        prop="qq"
        :rules="[
          {
            required: true,
            message: '必填项',
          },
          {
            validator: checkQQStrict,
            message: '请输入正确的QQ号',
            trigger: 'blur',
          },
        ]"
      >
        <el-input
          v-model.number="addUserForm.qq"
          autocomplete="off"
          placeholder="请输入QQ号"
        />
      </el-form-item>
      <!-- 用户组 -->
      <el-form-item
        label="用户组:"
        prop="group_name"
        :rules="[
          {
            required: true,
            message: '必选项',
          },
        ]"
      >
        <el-select v-model="addUserForm.group_name" placeholder="请选择用户组">
          <el-option
            v-for="(value, key, index) in groupName"
            :label="value"
            :value="toNumber(key)"
          />
        </el-select>
      </el-form-item>
      <!-- 用户状态 -->
      <el-form-item
        label="用户状态:"
        prop="status"
        :rules="[
          {
            required: true,
            message: '必填项',
          },
        ]"
      >
        <el-select v-model="addUserForm.status" placeholder="请选择用户状态">
          <el-option
            v-for="(value, key, index) in userStatus"
            :label="value"
            :value="toNumber(key)"
          />
        </el-select>
      </el-form-item>
    </el-form>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="userEditCancel">取消</el-button>
        <el-button type="primary" @click="userSubmit"> 提交 </el-button>
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

:deep(.el-card__body) {
  height: 100%;
}

// 根据用户状态显示行颜色
:deep(.close) {
  --el-table-tr-bg-color: var(--el-color-warning-light-8);
}
</style>
