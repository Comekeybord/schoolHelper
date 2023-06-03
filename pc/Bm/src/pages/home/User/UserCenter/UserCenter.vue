<script setup>
import { reactive, getCurrentInstance } from "vue";
import { useUserStore } from "@/stores/user";

import {
  checkPwdStrict,
  checkAccStrict,
  checkGroupNameStrict,
} from "@/model/formRules";
import { storeToRefs } from "pinia";

const store = useUserStore();
const { proxy } = getCurrentInstance();
// #region
/*
  定义页面数据
*/
// user信息
const { userInfo } = storeToRefs(store);
console.log(userInfo);

// 修改管理员表单
const adminInfo = reactive({
  account: "",
  oldPwd: "",
  newPwd: "",
  group_name: "",
});

// 修改管理员信息表单验证规则
const adminFormRules = reactive({
  account: [
    {
      required: true,
      message: "此项必填",
      trigger: "submit",
    },
    {
      validator: checkAccStrict,
      message: "4到16位（字母，数字，下划线，减号）",
      trigger: "blur",
    },
  ],
  oldPwd: [
    {
      required: true,
      message: "此项必填",
      trigger: "submit",
    },
    {
      validator: checkPwdStrict,
      message: "大写字母，小写字母，数字，特殊符号",
      trigger: "blur",
    },
  ],
  newPwd: [
    {
      required: true,
      message: "此项必填",
      trigger: "submit",
    },
    {
      validator: checkPwdStrict,
      message: "大写字母，小写字母，数字，特殊符号",
      trigger: "blur",
    },
  ],
  group_name: [
    {
      required: true,
      message: "此项必填",
      trigger: "submit",
    },
    {
      validator: checkGroupNameStrict,
      message: "中文和数字",
      trigger: "blur",
    },
  ],
});
// #endregion

// #region
/*
  页面方法
*/
// 修改管理员信息
async function adminEdit() {
  // 表单验证
  let flag = true;
  await proxy.$refs.adminRef.validate((valid) => {
    if (!valid) flag = false;
  });
  // console.log(flag);
  if (!flag) return;
  const res = await proxy.$api.adminEdit(adminInfo);
  // console.log(res);
  if (res.code !== 200) {
    ElMessage({
      message: res.msg,
      type: "error",
    });
    return;
  }

  ElMessage({
    message: res.msg,
    type: "success",
  });
}

// 重置表单
async function formReset() {
  await proxy.$refs.adminRef.resetFields();
}
// #endregion
</script>

<template>
  <!-- layout 布局 分为24份 -->
  <el-row :gutter="20" style="height: 68%">
    <!-- gutter为列间距 -->
    <el-col :span="8">
      <!-- 左列占八分 -->

      <!-- 用户卡片 -->
      <el-card shadow="hover" style="height: 100%">
        <div class="user-info">
          <div class="user_info">
            <img src="@/assets/touxiang/kkk.jpeg" alt="" />
            <span>
              <span>{{ userInfo.account }}</span>
              <span>{{ userInfo.group_name }}</span>
            </span>
          </div>

          <!-- 登录时间地点 -->
          <div class="login_info">
            <span>上次登录时间:</span>
            <span>上次登录地点:</span>
          </div>
        </div>
      </el-card>
    </el-col>

    <el-col :span="16">
      <!-- 右列占16份 -->

      <el-card shadow="hover" style="height: 100%">
        <h3>修改管理员信息</h3>
        <el-form
          :label-position="right"
          label-width="100px"
          :model="adminInfo"
          style="max-width: 388px; margin: 20px"
          ref="adminRef"
          :rules="adminFormRules"
        >
          <el-form-item label="用户账号" prop="account">
            <el-input v-model.trim="adminInfo.account" />
          </el-form-item>
          <el-form-item label="旧密码" prop="oldPwd">
            <el-input type="password" v-model.trim="adminInfo.oldPwd" />
          </el-form-item>
          <el-form-item label="新密码" prop="newPwd">
            <el-input type="password" v-model.trim="adminInfo.newPwd" />
          </el-form-item>
          <el-form-item label="所属组" prop="group_name">
            <el-input v-model.trim="adminInfo.group_name" />
          </el-form-item>
          <el-form-item>
            <el-button @click="adminEdit">确认修改</el-button>
            <el-button @click="formReset">重置表单</el-button>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
  </el-row>
</template>

<style lang="less" scoped>
.user-info {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  height: 100%;

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
}
</style>
