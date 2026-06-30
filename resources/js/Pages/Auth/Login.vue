<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const showPassword = ref(false)

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Iniciar Sesión" />

    <div class="mb-6 text-center">
      <img
        alt="Logo Finca"
        class="h-20 w-20 object-contain mb-2 mx-auto"
        src="https://lh3.googleusercontent.com/aida/AP1WRLujkvGC--UynOHGzU51ms0UZgR8shNQRpOJiGh1blC6KqLnQ6LKT4ExLkelgz1lYqj2LXesjL75Kv4czM84YYkfAN_Ncu0E6FztqOSTiYiJQlPRHygXogRZe8j7bHqJ2jKNMM2nSBUK5wg2lfzJSS3faJCjpj6iznX64ZGFfxnDO2gi7PX2C0vrEuIwbvkNJg2dNUl33N7uTbsI9gUwQBB8OrbRVzqdCqEMPYteQ4aNp7LBq43ZrnhzE6te"
      />
      <h1 class="text-2xl text-primary font-bold tracking-tight">Finca</h1>
      <p class="text-base text-on-surface-variant mt-1">Gestión Agrícola Inteligente</p>
    </div>

    <div v-if="status" class="mb-4 text-sm font-medium text-primary">{{ status }}</div>

    <form @submit.prevent="submit" class="w-full space-y-4">
      <div class="space-y-1">
        <label class="text-sm font-medium text-on-surface flex items-center gap-1" for="email">
          <span class="material-symbols-outlined text-[18px]">mail</span>
          Correo Electrónico
        </label>
        <input
          id="email"
          v-model="form.email"
          class="w-full h-12 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline"
          placeholder="ejemplo@finca.com"
          type="email"
          required
          autofocus
          autocomplete="username"
        />
        <p v-if="form.errors.email" class="text-xs text-error mt-1">{{ form.errors.email }}</p>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium text-on-surface flex items-center gap-1" for="password">
          <span class="material-symbols-outlined text-[18px]">lock</span>
          Contraseña
        </label>
        <div class="relative">
          <input
            id="password"
            v-model="form.password"
            class="w-full h-12 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline"
            :type="showPassword ? 'text' : 'password'"
            placeholder="••••••••"
            required
            autocomplete="current-password"
          />
          <button
            type="button"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors"
            @click="showPassword = !showPassword"
          >
            <span class="material-symbols-outlined">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
          </button>
        </div>
        <p v-if="form.errors.password" class="text-xs text-error mt-1">{{ form.errors.password }}</p>
      </div>

      <div class="flex items-center justify-between mt-2">
        <label class="flex items-center gap-2 cursor-pointer group">
          <input
            v-model="form.remember"
            class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary-fixed"
            type="checkbox"
          />
          <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Recordarme</span>
        </label>
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-secondary hover:text-tertiary font-semibold transition-colors"
        >
          ¿Olvidó su contraseña?
        </Link>
      </div>

      <button
        type="submit"
        class="w-full h-12 mt-6 bg-primary-container text-on-primary-container font-semibold rounded-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2"
        :disabled="form.processing"
      >
        {{ form.processing ? 'Iniciando...' : 'Iniciar Sesión' }}
        <span class="material-symbols-outlined">trending_flat</span>
      </button>
    </form>

    <div class="mt-8 w-full border-t border-surface-container-high pt-6 text-center">
      <p class="text-base text-on-surface-variant">¿Es un productor nuevo?</p>
      <div class="mt-3 flex flex-col gap-3">
        <Link
          :href="route('register')"
          class="w-full py-2 px-4 text-primary font-semibold text-sm border border-primary rounded-lg hover:bg-primary-fixed-dim transition-colors text-center block"
        >
          Solicitar Acceso
        </Link>
        <a class="flex items-center justify-center gap-1 text-sm text-outline hover:text-on-surface transition-colors" href="#">
          <span class="material-symbols-outlined text-[16px]">support_agent</span>
          Contactar Soporte Técnico
        </a>
      </div>
    </div>
  </GuestLayout>
</template>
