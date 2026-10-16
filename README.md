<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Livewire-4-00A3FF?logo=livewire" />
  <img src="https://img.shields.io/badge/React-19-61DAFB?logo=react" />
  <img src="https://img.shields.io/badge/Three.js-R3F-000000?logo=three.js" />
  <img src="https://img.shields.io/badge/Tailwind-3.4-06B6D4?logo=tailwindcss" />
  <img src="https://img.shields.io/badge/License-MIT-green" />
</p>

<h1 align="center">✨ Rendix — رندیکس</h1>

<p align="center">
  <strong>پلتفرم تخصصی طراحی وب‌سایت، سئو و دیجیتال مارکتینگ</strong><br/>
  A modern, high-performance, animated web platform built with Laravel 10, Livewire 4, React 19, Three.js, and GSAP.
</p>

<p align="center">
  <a href="#overview">معرفی</a> •
  <a href="#tech-stack">تکنولوژی‌ها</a> •
  <a href="#features">ویژگی‌ها</a> •
  <a href="#getting-started">شروع سریع</a> •
  <a href="#project-structure">ساختار پروژه</a>
</p>

---

## 🌐 Overview / معرفی

**رندیکس** یک پلتفرم وب‌سایت مدرن، های‌پرفورمنس و انیمیشنی است که با هدف ارائه‌ی خدمات تخصصی طراحی وب‌سایت، سئو و دیجیتال مارکتینگ (با تمرکز ویژه روی بازار محلی نجف‌آباد، اصفهان و سراسر ایران) توسعه داده شده است. این پروژه با ترکیب قدرت بک‌اند لاراول و پویایی فرانت‌اند React/3D، استانداردهای UI/UX جهانی را پیاده‌سازی کرده است.

**Rendix** is a modern, high-performance, animation-rich web platform offering professional web design, SEO, and digital marketing services. Built with a hybrid architecture—Laravel powering the routing, SEO, and layouts; Livewire (Volt) handling SPA-style navigation; and React + Three.js delivering an immersive visual layer—Rendix sets a new standard for agency websites.

---

## 🛠️ Tech Stack

| لایه / Layer | تکنولوژی / Technology |
|:---|:---|
| **بک‌اند** | Laravel 10 |
| **SPA Navigation** | Livewire 4 + Volt 1.x (`wire:navigate`) |
| **فرانت‌اند** | React 19 + Vite 5 |
| **3D Graphics** | Three.js + React Three Fiber + `@react-three/drei` |
| **Animations** | GSAP 3 + `@gsap/react` |
| **Styling** | Tailwind CSS 3.4 + RTL + Dark/Light mode |
| **Typography** | Vazirmatn (Persian) |
| **Build Tool** | Vite + `laravel-vite-plugin` |

---

## ✨ Features

- 🌌 **Persistent 3D Background** — GPU-accelerated Particle Plexus Network using `Points` & `LineSegments` that reacts to mouse movement and scroll
- 🌓 **Smart Dark/Light Mode** — `localStorage`-persisted theme with zero flash on initial load
- 🚀 **SPA-like Navigation** — seamless page transitions via `wire:navigate` without page reloads
- 📱 **Mobile-First Responsive** — Bento Grid system + Floating Pill Navbar
- 🔍 **SEO-Optimized** — SSR-friendly Blade pages with per-page meta tags, JSON-LD schema, and sitemap
- ⚡ **High Performance** — Lazy-loaded 3D canvas, optimized font loading, reduced-motion support
- 🇮🇷 **Full RTL Support** — Native right-to-left layout with Vazirmatn font
- 🎬 **GSAP Reveal Animations** — Content enters the viewport with smooth choreography
- ♿ **Accessibility** — `prefers-reduced-motion`, `aria-hidden` on canvas, keyboard navigation

---

## 🚀 Getting Started

### Prerequisites

- PHP ≥ 8.1
- Composer
- Node.js ≥ 18
- MySQL / PostgreSQL / SQLite

### Installation

```bash
# Clone the repository
git clone https://github.com/mahourkimiaeifar/Rendix.git
cd Rendix

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env, then run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
php artisan serve
npm run dev