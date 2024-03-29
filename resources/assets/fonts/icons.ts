export type IconsId =
  | "danger"
  | "delivery"
  | "endereco"
  | "funil"
  | "megafone"
  | "type-car"
  | "type-moto"
  | "type-truck";

export type IconsKey =
  | "Danger"
  | "Delivery"
  | "Endereco"
  | "Funil"
  | "Megafone"
  | "TypeCar"
  | "TypeMoto"
  | "TypeTruck";

export enum Icons {
  Danger = "danger",
  Delivery = "delivery",
  Endereco = "endereco",
  Funil = "funil",
  Megafone = "megafone",
  TypeCar = "type-car",
  TypeMoto = "type-moto",
  TypeTruck = "type-truck",
}

export const ICONS_CODEPOINTS: { [key in Icons]: string } = {
  [Icons.Danger]: "61697",
  [Icons.Delivery]: "61698",
  [Icons.Endereco]: "61699",
  [Icons.Funil]: "61700",
  [Icons.Megafone]: "61701",
  [Icons.TypeCar]: "61702",
  [Icons.TypeMoto]: "61703",
  [Icons.TypeTruck]: "61704",
};
