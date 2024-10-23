<template>
    <Teleport to="body">
      <Transition leave-active-class="duration-200">
        <div
          v-show="show"
          class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
        >
          <!-- Camada de fundo com efeito de blur -->
          <div
            class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
            @click="close"
          />
          
          <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <div
              v-show="show"
              class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full"
              :class="maxWidthClass"
            >
              <div class="p-6">
                <slot /> <!-- Usar slot para passar conteúdo do modal -->
                <div class="flex justify-end mt-4">
                  <button class="bg-blue-500 text-white px-4 py-2 rounded" @click="close">Fechar</button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </template>
  
  <script setup>
  import { computed, onMounted, onUnmounted, watch } from 'vue';
  
  const props = defineProps({
    show: {
      type: Boolean,
      default: false,
    },
    maxWidth: {
      type: String,
      default: '2xl',
    },
    closeable: {
      type: Boolean,
      default: true,
    },
  });
  
  const emit = defineEmits(['close']);
  
  // Controla o comportamento do modal ao abrir e fechar
  watch(
    () => props.show,
    () => {
      document.body.style.overflow = props.show ? 'hidden' : null;
    },
  );
  
  const close = () => {
    if (props.closeable) {
      emit('close');
    }
  };
  
  const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
      close();
    }
  };
  
  onMounted(() => document.addEventListener('keydown', closeOnEscape));
  onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = null;
  });
  
  // Classes para controle de largura
  const maxWidthClass = computed(() => {
    return {
      sm: 'sm:max-w-sm',
      md: 'sm:max-w-md',
      lg: 'sm:max-w-lg',
      xl: 'sm:max-w-xl',
      '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
  });
  </script>
  