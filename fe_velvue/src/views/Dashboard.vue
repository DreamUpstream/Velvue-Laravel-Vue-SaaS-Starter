<script setup>
import BestSellingWidget from "@/components/dashboard/uikit/BestSellingWidget.vue";
import NotificationsWidget from "@/components/dashboard/uikit/NotificationsWidget.vue";
import RecentSalesWidget from "@/components/dashboard/uikit/RecentSalesWidget.vue";
import RevenueStreamWidget from "@/components/dashboard/uikit/RevenueStreamWidget.vue";
import StatsWidget from "@/components/dashboard/uikit/StatsWidget.vue";
</script>

<template>
  <div class="grid grid-cols-12 gap-8">
    <StatsWidget />

    <div class="col-span-12 xl:col-span-6">
      <RecentSalesWidget />
      <BestSellingWidget />
    </div>
    <div class="col-span-12 xl:col-span-6">
      <RevenueStreamWidget />
      <NotificationsWidget />
    </div>
  </div>
</template>
