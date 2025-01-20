import { computed, reactive, ref, watch } from "vue";
import { useRoute } from "vue-router";

const activeTitle = ref("");

const layoutConfig = reactive({
  preset: "Aura",
  primary: "slate",
  surface: null,
  darkTheme: false,
  menuMode: "static",
});

const layoutState = reactive({
  activeMenuItem: null,
});

export function useLayout() {
  const route = useRoute();

  watch(
    () => route.meta.title,
    (newTitle) => {
      activeTitle.value = newTitle || "";
    },
    { immediate: true }
  );

  const setActiveMenuItem = (item) => {
    layoutState.activeMenuItem = item.value || item;
  };

  const toggleMenu = () => {
    if (layoutConfig.menuMode === "overlay") {
      layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
    }

    if (window.innerWidth > 991) {
      layoutState.staticMenuDesktopInactive =
        !layoutState.staticMenuDesktopInactive;
    } else {
      layoutState.staticMenuMobileActive = !layoutState.staticMenuMobileActive;
    }
  };

  const isSidebarActive = computed(
    () => layoutState.overlayMenuActive || layoutState.staticMenuMobileActive
  );

  const getPrimary = computed(() => layoutConfig.primary);

  const getSurface = computed(() => layoutConfig.surface);

  return {
    layoutConfig,
    layoutState,
    toggleMenu,
    isSidebarActive,
    getPrimary,
    getSurface,
    setActiveMenuItem,
    activeTitle,
  };
}
