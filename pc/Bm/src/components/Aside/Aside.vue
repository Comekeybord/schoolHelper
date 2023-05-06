<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

// 定义跳转路由的方法
function toRoute(route) {
  router.push({
    name: route.name,
  });
}
</script>

<template>
  <el-menu
    active-text-color="#ffd04b"
    background-color="#545c64"
    class="aside"
    default-active="1-1"
    text-color="#fff"
  >
    <!-- 多级菜单 -->
    <!-- 用户管理 -->
    <el-sub-menu index="1">
      <template #title>
        <el-icon><user /></el-icon>
        <span>用户</span>
      </template>
      <!-- 用户管理页面 -->
      <el-menu-item index="1-1" @click="toRoute({ name: 'userManage' })">
        <template #title>
          <el-icon><i class="iconfont icon-user"></i></el-icon>
          <span>用户管理</span>
        </template>
      </el-menu-item>

      <!-- 用户组管理页面 -->
      <el-menu-item index="1-2" @click="toRoute({ name: 'userGroup' })">
        <template #title>
          <el-icon><i class="iconfont icon-userGroup"></i></el-icon>
          <span>用户组</span>
        </template>
      </el-menu-item>

      <!-- 用户反馈页面 -->
      <el-menu-item index="1-3" @click="toRoute({ name: 'userFeeds' })">
        <template #title>
          <el-icon><i class="iconfont icon-userFeeds"></i></el-icon>
          <span>用户反馈</span>
        </template>
      </el-menu-item>
    </el-sub-menu>

    <!-- 系统设置 -->
    <el-sub-menu index="2">
      <template #title>
        <el-icon><i class="iconfont icon-system"></i></el-icon>
        <span>系统</span>
      </template>

      <!-- 系统设置设置当前的一些基本参数，例如系统语言、时区、系统通知等 -->
      <el-menu-item index="2-1" @click="toRoute({ name: 'config' })">
        <template #title>
          <el-icon><i class="iconfont icon-setting"></i></el-icon>
          <span>系统设置</span>
        </template>
      </el-menu-item>

      <!-- 安全管理 账户安全设置，例如设置密码复杂度、开启多因素认证等 -->
      <el-menu-item index="2-2" @click="toRoute({ name: 'safe' })">
        <template #title>
          <el-icon><i class="iconfont icon-safe"></i></el-icon>
          <span>安全管理</span>
        </template>
      </el-menu-item>

      <!-- 登录记录 获取系统管理后台登录记录 -->
      <el-menu-item index="2-3" @click="toRoute({ name: 'loginlog' })">
        <template #title>
          <el-icon><i class="iconfont icon-loginLog"></i></el-icon>
          <span>登录记录</span>
        </template>
      </el-menu-item>
    </el-sub-menu>
  </el-menu>
</template>

<style lang="less" scoped>
.aside {
  height: 100%;
  width: 300px;
}
</style>
