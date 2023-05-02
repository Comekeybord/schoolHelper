<script setup>
import { useUserStore } from "@/stores/user";
import Aside from "@/components/Aside/Aside.vue";
import Header from "@/components/Header/Header.vue";

const store = useUserStore();
</script>

<template>
  <div class="common-layout">
    <el-container>
      <el-header>
        <Header></Header>
      </el-header>
      <el-container>
        <el-aside width="200px">
          <Aside></Aside>
        </el-aside>
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
}

:deep(.el-container) {
  height: 100%;
}
</style>
