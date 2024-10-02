import { GrapesJsProducts } from './GrapesJsProducts';
import { UserText } from './UserText';
import { BlogPaymentsList } from 'components/Pages/BlogArticle/BlogPaymentList/BlogPaymentList';
import { BlogTransportsList } from 'components/Pages/BlogArticle/BlogTransportsList/BlogTransportsList';
import { useProductsByCatnums } from 'graphql/requests/products/queries/ProductsByCatnumsQuery.generated';
import { memo } from 'react';
import { GJS_PRODUCTS_SEPARATOR, parseCatnums } from 'utils/parsing/grapesJsParser';


type GrapesJsParserProps = {
    text: string;
    isBlogPage?: boolean;
};

export const GrapesJsParser: FC<GrapesJsParserProps> = memo(({ text, isBlogPage = false }) => {
    const catnums = parseCatnums(text);
    const [{ data: productsData, fetching: areProductsFetching }] = useProductsByCatnums({
        variables: { catnums },
    });

    const dividedParts = text.split(GJS_PRODUCTS_SEPARATOR).filter(Boolean);

    return (
        <>
            {dividedParts.map((part, index) => {
                if (part.match(/\[gjc-comp-FulfillmentList&#61;payment\]/g)) {
                    return <BlogPaymentsList key={'blogPayment_'+index} />;
                }

                if (part.match(/\[gjc-comp-FulfillmentList&#61;shipping\]/g)) {
                    return <BlogTransportsList key={'blogTransport_'+index} />;
                }
                
                if (part.match(/\[gjc-comp-(.*?)\]/g)) {
                    return (
                        <GrapesJsProducts
                            key={index}
                            allFetchedProducts={productsData}
                            areProductsFetching={areProductsFetching}
                            isBlogPage={isBlogPage}
                            rawProductPart={part}
                        />
                    );
                }

                return <UserText key={index} isGrapesJs htmlContent={part} />;
            })}
        </>
    );
});

GrapesJsParser.displayName = 'GrapesJsParser';