<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    AudioWaveform,
    BookOpen,
    Command,
    Folder,
    Frame,
    GalleryVerticalEnd,
    Map,
    PieChart,
    Shield,
    SquareTerminal,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const data = {
    user: {
        name: 'shadcn',
        email: 'm@example.com',
        avatar: '/avatars/shadcn.jpg',
    },
    teams: [
        {
            name: 'Acme Inc',
            logo: GalleryVerticalEnd,
            plan: 'Enterprise',
        },
        {
            name: 'Acme Corp.',
            logo: AudioWaveform,
            plan: 'Startup',
        },
        {
            name: 'Evil Corp.',
            logo: Command,
            plan: 'Free',
        },
    ],
    navMain: [
        {
            title: 'Tickets',
            url: '#',
            icon: SquareTerminal,
            isActive: usePage().url.startsWith('/tickets'),
            items: [
                {
                    title: 'Categorias',
                    url: route('tickets.category.index'),
                },
                {
                    title: 'Prioridades',
                    url: route('tickets.priority.index'),
                },
                {
                    title: 'Estados',
                    url: route('tickets.status.index'),
                },
                {
                    title: 'Gestion de Tickets',
                    url: route('tickets.tickets.index'),
                },
                {
                    title: 'Nuevo Ticket',
                    url: route('tickets.tickets.create'),
                },
            ],
        },
        {
            title: 'Administración',
            url: '#',
            icon: Shield,
            isActive: usePage().url.startsWith('/admin'),
            items: [
                {
                    title: 'Áreas',
                    url: route('admin.areas.index'),
                },
            ],
        },
    ],
    projects: [
        {
            name: 'Design Engineering',
            url: '#',
            icon: Frame,
        },
        {
            name: 'Sales & Marketing',
            url: '#',
            icon: PieChart,
        },
        {
            name: 'Travel',
            url: '#',
            icon: Map,
        },
    ],
};

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="data.navMain" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
