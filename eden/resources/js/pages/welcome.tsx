import Impact from '@/components/eden-components/impact';
import Hero from '@/components/eden-components/hero';
import { Stack, Divider } from '@mui/material';
import ProblemSection from '@/components/eden-components/problem-section';
import SolutionSection from '@/components/eden-components/solution-section';
import Footer from '@/components/eden-components/footer';

export default function Welcome() {
    return (
        <>
            <Hero />
            <SolutionSection />
            <ProblemSection />
            <Impact />
            <Footer />
        </>
    );
}
