<template>
  <div class="min-h-screen flex items-center justify-center relative overflow-hidden h-screen bg-[#fbf9f1]">
    <div class="fixed inset-0 z-0 overflow-hidden">
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuA1YH4wyGRzE7fLib2voFll5WyT3zluoem8t0MfCRhfiS-WwbRSWoXCeKH72da8SG0ky1yzNtozaSoDYz8tOQCc9afits4Zg2WPYnAcLbS643QiJ53lm4lA8Rms7Gq3S7RQlws7qppKBDX_UW9mSfnxtCcmWmILQiGxBKMIxpL2xcsUBOeDw2KhVaobHkd_dV-fxS8W74YFAnDNY-Psh3OxoY2Wl-01Dm-BxjVfdgkodhwlGBhl3VBOfQN6t7fyPwh1bAWrlVCLaFRl')"></div>
      <div class="absolute inset-0" style="background-color: rgba(27, 28, 23, 0.45); backdrop-filter: blur(2px);"></div>
    </div>

    <main class="relative z-10 w-full max-w-md px-4 md:px-0">
      <div class="bg-white rounded-lg p-8 flex flex-col items-center" style="box-shadow: 0 4px 12px rgba(146, 76, 0, 0.08);">
        <slot />
      </div>

      <div class="mt-8 flex items-center justify-center gap-4 text-white/80 text-xs">
        <span class="flex items-center gap-1">
          <span class="material-symbols-outlined text-[14px]">language</span>
          Español (Nicaragua)
        </span>
        <span class="w-1 h-1 bg-white/40 rounded-full"></span>
        <span>v2.4.0-Estable</span>
      </div>
    </main>
  </div>
</template>
