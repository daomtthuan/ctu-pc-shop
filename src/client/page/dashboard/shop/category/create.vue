<template>
  <div>
    <b-breadcrumb class="bg-light">
      <b-breadcrumb-item text="Bảng điều khiến" to="/dashboard"></b-breadcrumb-item>
      <b-breadcrumb-item text="Cửa hàng - Danh mục" to="/dashboard/shop/category"></b-breadcrumb-item>
      <b-breadcrumb-item text="Tạo mới" :to="$route.path"></b-breadcrumb-item>
    </b-breadcrumb>
    <hr />
    <c-dashboard-form-create-shop-category></c-dashboard-form-create-shop-category>
  </div>
</template>

<script lang="ts">
  import { Component, Vue } from 'nuxt-property-decorator';

  @Component({
    name: 'page-dashboard-shop-category-create',
    head: {
      title: 'Bảng điều khiển - Cửa hàng - Danh mục - Tạo mới',
    },
  })
  export default class extends Vue {}
</script>
