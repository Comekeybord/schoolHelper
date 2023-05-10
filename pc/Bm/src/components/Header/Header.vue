<script setup>
import { usePageStore } from "@/stores/page";
import { useUserStore } from "@/stores/user";
import { useRouter } from "vue-router";

const pageStore = usePageStore();
const userStore = useUserStore();
const router = useRouter();

// 跳转路由
const toRoute = (option) => {
  router.push({
    name: option.name,
  });
};

// 退出后台
async function out() {
  await router.push({
    name: "login",
  });

  userStore.loginOut();

  ElMessage({
    message: "退出成功",
    type: "success",
  });
}
</script>

<template>
  <div class="container">
    <!-- 面包屑 -->
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ name: 'userCenter' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item
        :to="{ name: pageStore.currentPageName }"
        v-if="pageStore.currentPageName !== 'userCenter'"
        >{{ pageStore.currentPageName }}</el-breadcrumb-item
      >
    </el-breadcrumb>

    <!-- 用户头像(下拉菜单) -->
    <el-dropdown class="dropdown">
      <img src="@/assets/touxiang/kkk.jpeg" alt="" />

      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item @click="toRoute({ name: 'userCenter' })"
            >个人主页</el-dropdown-item
          >
          <el-dropdown-item @click="out()">退出</el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>

<style lang="less" scoped>
.container {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;

  :deep(.el-breadcrumb__inner) {
    color: #fff;
  }

  .dropdown {
    img {
      width: 40px;
      height: 40px;
      outline: none;
      border-radius: 50%;
    }
  }
}
</style>
