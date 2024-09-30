/* TODO FE
import { useBlogPaymentsQueryApi } from 'graphql/generated';
import useTranslation from 'next-translate/useTranslation';
import { twJoin } from 'tailwind-merge';
import { useFormatPrice } from 'utils/formatting/useFormatPrice';

export const BlogPaymentsList: FC = () => {
    const [{ data }] = useBlogPaymentsQueryApi();
    const formatPrice = useFormatPrice();
    const { t } = useTranslation();

    return (
        <ul className="gjs-fulfillment-list">
            {data?.freeFulfillmentLimitWithVat && (
                <li className="text-green">
                    <span>
                        {t('Order above {{ price }}', {
                            price: formatPrice(parseFloat(data.freeFulfillmentLimitWithVat)),
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
*/
