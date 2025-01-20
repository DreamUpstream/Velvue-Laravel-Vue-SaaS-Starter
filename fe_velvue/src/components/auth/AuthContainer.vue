<template>
  <div
    class="bg-surface-50 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden"
  >
    <div class="flex flex-col items-center justify-center">
      <div style="border-radius: 56px; padding: 0.3rem" class="bg-slate-50">
        <div
          class="w-full bg-surface-0 py-10 px-8 sm:px-20 shadow-xl border border-gray-300"
          style="border-radius: 53px"
        >
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
