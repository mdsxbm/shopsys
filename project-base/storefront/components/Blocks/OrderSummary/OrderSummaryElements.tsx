import { twJoin } from 'tailwind-merge';

export const OrderSummaryContent: FC = ({ children }) => <div className="relative flex flex-col">{children}</div>;

export const OrderSummaryRowWrapper: FC = ({ children }) => (
    <div className={twJoin('mb-5 border-b border-borderAccent pb-3 w-full')}>{children}</div>
);

export const OrderSummaryRow: FC = ({ children }) => <div className="flex justify-between w-full">{children}</div>;

export const OrderSummaryTextAndImage: FC = ({ children }) => (
    <div className="flex items-center gap-2 align-baseline text-sm">{children}</div>
);

export const OrderSummaryPrice: FC = ({ children }) => <div className="text-sm">{children}</div>;
