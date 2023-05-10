<script setup>
import { getCurrentInstance, onMounted, reactive } from "vue";

const { proxy } = getCurrentInstance();

// 渲染前请求数据
onMounted(() => {
  getUserList();
});

// 定义用户数据列表
const userList = reactive([]);
// 定义搜索框数据
const formInline = reactive({
  name: "",
});

// 请求用户数据
const getUserList = async () => {
  const res = await proxy.$api.getUserList();
  const userObjT = res.data;

  for (var k in userObjT) {
    // 遍历用户对象，把所有用户对象都放入用户列表中
    userObjT[k].forEach((item) => {
      userList.push(item);
    });
  }
  // console.log(res);
};
</script>

<template>
  <!-- 用户管理页头部，添加 搜索 -->
  <div class="user_header">
    <el-button type="primary">+添加用户</el-button>
    <!-- 搜索框 -->
    <el-form :inline="true" :model="formInline" class="search-form">
      <el-form-item label="请输入关键字">
        <el-input v-model="formInline.name" placeholder="请输入关键字" />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="handleSearch">搜索</el-button>
      </el-form-item>
    </el-form>
  </div>

  <!-- 用户列表 -->
  <el-card style="height: 85%">
    <el-table
      :data="userList"
      height="100%"
      style="width: 100%; text-align: center"
    >
      <el-table-column prop="uid" label="用户ID" />
      <el-table-column prop="account" label="用户账号" />
      <el-table-column prop="name" label="用户名" />
      <el-table-column prop="phone" label="用户手机" />
      <el-table-column prop="qq" label="用户QQ" />
      <el-table-column prop="group_name" label="用户所属组" />
      <el-table-column prop="status" label="用户状态" />
    </el-table>
  </el-card>
  <el-row style="display: flex; justify-content: center; margin-top: 8px">
    <el-pagination
      background
      layout="prev, pager, next"
      :total="userList.length"
      hide-on-single-page="true"
    />
  </el-row>
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
</style>
