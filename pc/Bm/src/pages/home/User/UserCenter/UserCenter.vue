<script setup></script>

<template>
  <!-- layout 布局 分为24份 -->
  <el-row :gutter="20">
    <!-- gutter为列间距 -->
    <el-col :span="8">
      <!-- 左列占八分 -->

      <!-- 用户卡片 -->
      <el-card shadow="hover">
        <div class="user_info">
          <img src="@/assets/touxiang/kkk.jpeg" alt="" />
          <span>
            <span>Admin</span>
            <span>超级管理员</span>
          </span>
        </div>

        <!-- 登录时间地点 -->
        <div class="login_info">
          <span>上次登录时间:</span>
          <span>上次登录地点:</span>
        </div>
      </el-card>
    </el-col>

    <el-col :span="16">
      <!-- 右列占16份 -->
      右列
    </el-col>
  </el-row>
</template>

<style lang="less" scoped>
.user_info {
  display: flex;
  justify-content: space-around;
  img {
    display: inline-block;
    width: 168px;
    border-radius: 50%;
  }

  & > span {
    span {
      display: block;
      padding: 5px 0;
    }

    span:first-child {
      margin-top: 28px;
      font-weight: 700;
      font-size: 22px;
    }
  }
}

.login_info {
  padding: 0 20px;
  display: flex;
  flex-direction: column;

  span {
    margin: 5px 0;
  }
}
</style>
