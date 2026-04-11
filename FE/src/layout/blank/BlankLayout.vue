<template>
  <div class="blank-layout">
    <div class="blank-layout__glow blank-layout__glow--one"></div>
    <div class="blank-layout__glow blank-layout__glow--two"></div>
    <div class="blank-layout__grid"></div>

    <main class="blank-layout__content">
      <slot />
    </main>
  </div>
</template>

<script>
export default {
  name: 'BlankLayout',
};
</script>

<style scoped>
.blank-layout {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  background:
    radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 24%),
    radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.12), transparent 26%),
    linear-gradient(180deg, #eef7ff 0%, #f7fbff 36%, #ffffff 100%);
}

.blank-layout__content {
  position: relative;
  z-index: 2;
  min-height: 100vh;
}

.blank-layout__grid {
  position: absolute;
  inset: 0;
  z-index: 0;
  background-image:
    linear-gradient(rgba(148, 163, 184, 0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(148, 163, 184, 0.08) 1px, transparent 1px);
  background-size: 42px 42px;
  mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.24), transparent 72%);
}

.blank-layout__glow {
  position: absolute;
  z-index: 1;
  border-radius: 999px;
  filter: blur(10px);
  opacity: 0.75;
}

.blank-layout__glow--one {
  top: -120px;
  left: -110px;
  width: 320px;
  height: 320px;
  background: rgba(59, 130, 246, 0.18);
}

.blank-layout__glow--two {
  right: -90px;
  bottom: -140px;
  width: 300px;
  height: 300px;
  background: rgba(16, 185, 129, 0.18);
}
</style>
