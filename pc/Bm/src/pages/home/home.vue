<script setup>
import { useUserStore } from "@/stores/user";
import Aside from "@/components/Aside/Aside.vue";
import Header from "@/components/Header/Header.vue";

const store = useUserStore();
</script>

<template>
  <div class="common-layout">
    <el-container>
      <el-aside width="200px">
        <Aside></Aside>
      </el-aside>
      <el-container>
        <el-header class="el-header">
          <Header></Header>
        </el-header>
        <el-main>
          <router-view name="mainContent"></router-view>
        </el-main>
      </el-container>
    </el-container>
  </div>
</template>

<style lang="less" scoped>
.common-layout {
  height: 100%;

  .el-header {
    background-color: #333;
  }
}

:deep(.el-container) {
  height: 100%;
}
</style>
