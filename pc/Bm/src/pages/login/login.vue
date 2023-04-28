<script setup>
import { reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
const route = useRoute();
const router = useRouter();

// console.log(route,router);

// 登录表单数据
const loginForm = reactive({
  username: "",
  password: "",
});
</script>

<template>
  <el-form
    :label-position="right"
    label-width="48px"
    :model="loginForm"
    style="max-width: 460px"
    class="loginForm"
  >
    <h3>智洐校园后台管理系统登录</h3>
    <el-form-item label="账号" prop="username">
      <el-input v-model="loginForm.username" />
    </el-form-item>
    <el-form-item label="密码" prop="password">
      <el-input v-model="loginForm.password" />
    </el-form-item>
    <el-form-item>
      <el-button type="primary">登录</el-button>
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
