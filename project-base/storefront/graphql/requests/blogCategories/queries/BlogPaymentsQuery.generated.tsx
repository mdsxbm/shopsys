import * as Types from '../../../types';

import gql from 'graphql-tag';
import { PriceFragment } from '../../prices/fragments/PriceFragment.generated';
import * as Urql from 'urql';
export type Omit<T, K extends keyof T> = Pick<T, Exclude<keyof T, K>>;
export type TypeBlogPaymentsQueryVariables = Types.Exact<{ [key: string]: never; }>;


export type TypeBlogPaymentsQuery = { __typename?: 'Query', settings: { __typename?: 'Settings', pricing: { __typename?: 'PricingSetting', freeTransportAndPaymentPriceWithVatLimit: string | null } } | null, payments: Array<{ __typename: 'Payment', uuid: string, name: string, price: { __typename: 'Price', priceWithVat: string, priceWithoutVat: string, vatAmount: string } }> };


      export interface PossibleTypesResultData {
        possibleTypes: {
          [key: string]: string[]
        }
      }
      const result: PossibleTypesResultData = {
  "possibleTypes": {
    "Advert": [
      "AdvertCode",
      "AdvertImage"
    ],
    "ArticleInterface": [
      "ArticleSite",
      "BlogArticle"
    ],
    "Breadcrumb": [
      "ArticleSite",
      "BlogArticle",
      "BlogCategory",
      "Brand",
      "Category",
      "Flag",
      "MainVariant",
      "RegularProduct",
      "Store",
      "Variant"
    ],
    "CustomerUser": [
      "CompanyCustomerUser",
      "RegularCustomerUser"
    ],
    "Hreflang": [
      "BlogArticle",
      "BlogCategory",
      "Brand",
      "Flag",
      "MainVariant",
      "RegularProduct",
      "SeoPage",
      "Variant"
    ],
    "NotBlogArticleInterface": [
      "ArticleLink",
      "ArticleSite"
    ],
    "ParameterFilterOptionInterface": [
      "ParameterCheckboxFilterOption",
      "ParameterColorFilterOption",
      "ParameterSliderFilterOption"
    ],
    "Product": [
      "MainVariant",
      "RegularProduct",
      "Variant"
    ],
    "ProductListable": [
      "Brand",
      "Category",
      "Flag"
    ],
    "Slug": [
      "ArticleSite",
      "BlogArticle",
      "BlogCategory",
      "Brand",
      "Category",
      "Flag",
      "MainVariant",
      "RegularProduct",
      "Store",
      "Variant"
    ]
  }
};
      export default result;
    

export const BlogPaymentsQueryDocument = gql`
    query BlogPaymentsQuery {
  settings {
    pricing {
      freeTransportAndPaymentPriceWithVatLimit
    }
  }
  payments {
    __typename
    uuid
    name
    price {
      ...PriceFragment
    }
  }
}
    ${PriceFragment}`;

export function useBlogPaymentsQuery(options?: Omit<Urql.UseQueryArgs<TypeBlogPaymentsQueryVariables>, 'query'>) {
  return Urql.useQuery<TypeBlogPaymentsQuery, TypeBlogPaymentsQueryVariables>({ query: BlogPaymentsQueryDocument, ...options });
};