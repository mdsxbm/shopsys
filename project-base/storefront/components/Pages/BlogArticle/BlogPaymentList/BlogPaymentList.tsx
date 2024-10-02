import { useBlogPaymentsQuery } from 'graphql/requests/blogCategories/queries/BlogPaymentsQuery.generated';
import useTranslation from 'next-translate/useTranslation';
import { twJoin } from 'tailwind-merge';
import { useFormatPrice } from 'utils/formatting/useFormatPrice';

export const BlogPaymentsList: FC = () => {
    const [{ data }] = useBlogPaymentsQuery();
    const formatPrice = useFormatPrice();
    const { t } = useTranslation();

    return (
        <ul className="gjs-fulfillment-list">
            {data?.settings?.pricing.freeTransportAndPaymentPriceWithVatLimit && (
                <li className="text-green">
                    <span>
                        {t('Order above {{ price }}', {
                            price: formatPrice(parseFloat(data.settings.pricing.freeTransportAndPaymentPriceWithVatLimit)),
                        })}
                    </span>
                    <span>{t('Free')}</span>
                </li>
            )}
            {data?.payments.map((payment) => (
                <li key={payment.uuid} className={twJoin(payment.price.priceWithVat[0] === '0' && 'text-green')}>
                    <span>{payment.name}</span>
                    <span>{formatPrice(parseFloat(payment.price.priceWithVat))}</span>
                </li>
            ))}
        </ul>
    );
};
