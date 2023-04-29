<script setup>
import { getCurrentInstance, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const { proxy } = getCurrentInstance(); // 获取全局属性代理

// console.log(proxy);

// console.log(route,router);

// 登录表单数据
const loginForm = reactive({
  account: "",
  password: "",
});

// 登录
async function submit() {
  const next = await proxy.$refs.loginRef.validate((validated) => {
    if (!validated) {
      ElMessage({
        type: "warning",
        message: "请填写账号密码!",
      });
      return false;
    }
  });
  if (!next) return;
  const res = await proxy.$api.login(loginForm);
  console.log(res);
  if (res.code !== 200)
    return ElMessage({
      message: "账号或密码错误",
      type: "warning",
    });

  ElMessage({
    message: "登录成功",
    type: "success",
  });
}

const rules = reactive({
  account: [
    {
      required: true,
      message: "请输入用户名",
      trigger: "blur",
    },
  ],
  password: [
    {
      required: true,
      message: "请输入密码",
      trigger: "blur",
    },
  ],
});
</script>

<template>
  <el-form
    :label-position="right"
    label-width="48px"
    :model="loginForm"
    style="max-width: 460px"
    class="loginForm"
    :rules="rules"
    hide-required-asterisk="true"
    status-icon
    ref="loginRef"
  >
    <!-- 提示语 -->
    <h3>智洐校园后台管理系统登录</h3>

    <!-- 账号输入框 -->
    <el-form-item label="账号" prop="account">
      <el-input v-model="loginForm.account" />
    </el-form-item>

    <!-- 密码输入框 -->
    <el-form-item label="密码" prop="password">
      <el-input v-model="loginForm.password" />
    </el-form-item>

    <!-- 登录按钮 -->
    <el-form-item>
      <el-button type="primary" @click="submit">登录</el-button>
    </el-form-item>
  </el-form>
</template>

<style lang="less" scoped>
.loginForm {
  margin: 180px auto;
  width: 350px;
  padding: 35px 35px 15px 35px;
  background-color: #fff;
  border: 1px solid #eaeaea;
  border-radius: 13px;
  box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);

  h3 {
    text-align: center;
    color: #505450;
    margin-bottom: 25px;
  }
  :deep(.el-form-item__content) {
    justify-content: center;
    margin-left: 0 !important;
  }
}
</style>
