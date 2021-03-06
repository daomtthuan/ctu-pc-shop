<template>
  <div>
    <b-breadcrumb class="bg-light">
      <b-breadcrumb-item text="Bảng điều khiến" to="/dashboard"></b-breadcrumb-item>
      <b-breadcrumb-item text="Cửa hàng - Đánh giá" :to="$route.path"></b-breadcrumb-item>
    </b-breadcrumb>
    <hr />
    <div v-if="$fetchState.pending" class="text-center"><b-spinner small></b-spinner> Đang tải...</div>
    <div v-else-if="!$fetchState.error">
      <b-form-group label="Nhóm danh mục:" label-for="select-category-group" label-size="sm">
        <b-form-select
          id="select-category-group"
          v-model="idCategoryGroupSelected"
          :options="categoryGroupOptions"
          size="sm"
          :disabled="categoryPending || productPending || reviewPending"
        ></b-form-select>
      </b-form-group>
      <div v-if="idCategoryGroupSelected != null">
        <div v-if="categoryPending" class="text-center"><b-spinner small></b-spinner> Đang tải...</div>
        <div v-else>
          <b-form-group label="Danh mục:" label-for="select-category" label-size="sm">
            <b-form-select
              id="select-category"
              v-model="idCategorySelected"
              :options="categoryOptions"
              :disabled="productPending || reviewPending"
              size="sm"
            ></b-form-select>
          </b-form-group>
          <div v-if="idCategorySelected != null">
            <div v-if="productPending" class="text-center"><b-spinner small></b-spinner> Đang tải...</div>
            <div v-else>
              <b-form-group label="Sản phẩm:" label-for="select-product" label-size="sm">
                <b-form-select id="select-product" v-model="idProductSelected" :options="productOptions" :disabled="reviewPending" size="sm"></b-form-select>
              </b-form-group>
              <div v-if="idProductSelected != null">
                <div v-if="reviewPending" class="text-center"><b-spinner small></b-spinner> Đang tải...</div>
                <c-dashboard-table
                  :title="`Danh sách đánh giá của sản phẩm ${nameProduct}`"
                  :items="items"
                  :fields="fields"
                  :notes="notes"
                  :row-class="rowClass"
                  :remove-item="remove"
                  v-else
                ></c-dashboard-table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import { Component, Vue, Watch } from 'nuxt-property-decorator';

  @Component({
    name: 'page-dashboard-shop-review',
    head: {
      title: 'Bảng điều khiển - Cửa hàng - Đánh giá',
    },
  })
  export default class extends Vue {
    private nameProduct: string | null = null;
    private items: Entity.Product[] = [];
    private fields: App.Component.Table.Field[] = [];
    private notes: App.Component.Table.Note[] = [{ label: 'Vô hiệu hoá', class: 'text-secondary bg-light font-weight-light' }];
    private idCategoryGroupSelected: number | null = null;
    private idCategorySelected: number | null = null;
    private idProductSelected: number | null = null;
    private categoryGroupOptions: App.Control.SeleteOption[] = [{ value: null, text: 'Chọn nhóm danh mục', disabled: true }];
    private categoryOptions: App.Control.SeleteOption[] = [];
    private productOptions: App.Control.SeleteOption[] = [];
    private categoryPending: boolean = false;
    private productPending: boolean = false;
    private reviewPending: boolean = false;

    public rowClass(item: Entity.Account) {
      return item.state ? null : 'text-secondary bg-light font-weight-light';
    }

    public async fetch() {
      try {
        let categoryGroups: Entity.CategoryGroup[] = (await this.$axios.get('/api/admin/category-group')).data;
        if (categoryGroups.length > 0) {
          this.categoryGroupOptions = [{ value: null, text: 'Chọn nhóm danh mục', disabled: true }];
          for (let categoryGroup of categoryGroups) {
            this.categoryGroupOptions.push({ value: categoryGroup.id, text: categoryGroup.name });
          }
        } else {
          this.categoryGroupOptions = [{ value: null, text: 'Không có nhóm danh mục nào', disabled: true }];
        }
      } catch (error) {
        this.$nuxt.error({ statusCode: (<Response>error.response).status });
      }
    }

    @Watch('idCategoryGroupSelected')
    public async onIdCategoryGroupSelectedChanged(newValue: number) {
      if (newValue != null) {
        try {
          this.categoryPending = true;
          let categories: Entity.Category[] = (await this.$axios.get('/api/admin/category', { params: { idCategoryGroup: newValue } })).data;
          if (categories.length > 0) {
            this.categoryOptions = [{ value: null, text: 'Chọn danh mục', disabled: true }];
            for (let category of categories) {
              this.categoryOptions.push({ value: category.id, text: category.name });
            }
          } else {
            this.categoryOptions = [{ value: null, text: 'Không có danh mục nào', disabled: true }];
          }
          this.idCategorySelected = null;
        } catch (error) {
          this.$nuxt.error({ statusCode: (<Response>error.response).status });
        } finally {
          this.categoryPending = false;
        }
      }
    }

    @Watch('idCategorySelected')
    public async onIdCategorySelectedChanged(newValue: number) {
      if (newValue != null) {
        try {
          this.productPending = true;
          let products: Entity.Product[] = (await this.$axios.get('/api/admin/product', { params: { idCategory: newValue } })).data;
          if (products.length > 0) {
            this.productOptions = [{ value: null, text: 'Chọn sản phẩm', disabled: true }];
            for (let product of products) {
              this.productOptions.push({ value: product.id, text: product.name });
            }
          } else {
            this.productOptions = [{ value: null, text: 'Không có sản phẩm nào', disabled: true }];
          }
          this.idProductSelected = null;
        } catch (error) {
          this.$nuxt.error({ statusCode: (<Response>error.response).status });
        } finally {
          this.productPending = false;
        }
      }
    }

    @Watch('idProductSelected')
    public async onIdProductSelectedChanged(newValue: number) {
      if (newValue != null) {
        try {
          this.reviewPending = true;
          this.nameProduct = this.productOptions.filter((product) => product.value == newValue)[0].text;
          this.items = (await this.$axios.get('/api/admin/review', { params: { idProduct: newValue } })).data;
          this.fields = [
            { key: 'id', label: 'Id', sortable: true, class: 'd-none' },
            { key: 'writer.username', label: 'Tài khoản', sortable: true, class: 'align-middle' },
            { key: 'writer.fullName', label: 'Họ và tên', sortable: true, class: 'align-middle' },
            { key: 'star', label: 'Số sao', sortable: true, class: 'align-middle text-md-right fit' },
            { key: 'content', label: 'Nội dung', sortable: true, class: 'align-middle' },
            {
              key: 'state',
              label: 'Trạng thái',
              sortable: true,
              class: 'd-none',
              formatter: (value) => (value == 1 ? 'Kích hoạt' : 'Vô hiệu hoá'),
              sortByFormatted: true,
              filterByFormatted: true,
            },
            { key: 'actions', label: 'Thao tác', class: 'align-middle fit' },
          ];
        } catch (error) {
          this.$nuxt.error({ statusCode: (<Response>error.response).status });
        } finally {
          this.reviewPending = false;
        }
      }
    }

    public async remove(id: number) {
      try {
        await this.$axios.delete('/api/admin/review', { params: { id } });
        this.items = this.items.filter((item) => item.id != id);
        this.$nuxt.$bvToast.toast('Đã xoá đánh giá.', {
          title: 'Xoá thành công!',
          variant: 'success',
          solid: true,
          toaster: 'b-toaster-bottom-right',
        });
      } catch (error) {
        this.$nuxt.error({ statusCode: (<Response>error.response).status });
      }
    }
  }
</script>
