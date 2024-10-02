import { HTMLMotionProps, m } from 'framer-motion';

export const AnimateAppearDiv: FC<HTMLMotionProps<'div'>> = ({ children, className, ...props }) => (
    <m.div
        animate={{ opacity: 1, scale: 1 }}
        className={className}
        exit={{ opacity: 0, scale: 0.2 }}
        initial={{ opacity: 0, scale: 0.2 }}
        transition={{ duration: 0.2 }}
        {...props}
    >
        {children}
    </m.div>
);
