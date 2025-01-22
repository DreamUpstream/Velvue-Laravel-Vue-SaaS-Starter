<template>
  <div class="bg-surface-50 flex items-center justify-center min-h-screen">
    <div class="flex flex-col items-center justify-center w-full px-4 sm:px-6">
      <div style="border-radius: 56px; padding: 0.3rem" class="bg-slate-50">
        <div
          class="w-full bg-surface-0 py-10 px-16 sm:px-20 w-[30rem] md:w-[36rem] card-container"
          style="border-radius: 53px"
        >
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
