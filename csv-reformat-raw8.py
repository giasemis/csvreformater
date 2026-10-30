#CSV reformating for prestashop
from os.path import isfile
import datetime

#functions for fields here
def field_Id(config):#requires settings["startID"]    
    if not config:
        return ";"
    else:
        settings["startID"] += 1
        return str(settings["startID"]-1) + ";"
    
def field_Active(config):# requires settings["isActive"]
    if not config:
        return ";"
    else:
        return settings["isActive"] + ";"
    
def field_Name(config,product):#product[0]
    if not config:
        return ";"
    else:
        return product[3] + ";"

def field_Category(config,product,index):#requires product,index,categories,productsMeta
    if not config:
        return ";"
    else:
        if productsMeta[index][1] in categories:
            for ids in categories[productsMeta[index][1]].keys():
                for permutation in categories[productsMeta[index][1]][ids]:
                    if product[1].lower() == permutation:
                        return ids +";"
        return ";"

def field_Price(config,product):#requires product[7], settings["tax"]
    if not config:
        return ";"
    else:        
        return str(round(float(product[7])/settings["tax"],2)) + ";"

def field_TaxRule(config):#requires settings["taxRule"]
    if not config:
        return ";"
    else:
        return settings["taxRule"]+";"

def field_WholeSale(config,product):#requires product[7], settings["wholesale_sale"]
    if not config:
        return ";"
    else:
        return str(round(float(product[7]) * (1-settings["wholesale_sale"]),2)) + ";"

def field_OnSale(config):#requires settings["onSale"]
    if not config:
        return ";"
    else:
        return settings["onSale"] + ";"

def field_DiscountFixed(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"
        
def field_EmptyField(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"        
        
        
        

def field_DiscountRelative(config):#requires settings["shopbay_sale"]
    if not config:
        return ";"
    else:
        return settings["shopbay_sale"]+";"
    
def field_DiscountFrom(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_DiscountTill(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Ref_Isbn(config,product):#requires product[6]
    if not config:
        return ";"
    else:
        return product[6] + ";"
    
def field_SupplierReference(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Supplier(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Manufacturer(config,product,index):#ekdotis requires product[0],productsMeta[index][0]
    if not config:
        return ";"
    else:
        return productsMeta[index][0] + ";"

def field_Ean13(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Upc(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Ecotax(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Width(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Height(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Depth(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Weight(config):#fixed weight not in settings
    if not config:
        return ";"
    else:
        return "520;"

def field_Quantity(config):#fixed quantity not in settings
    if not config:
        return ";"
    else:
        return "30;"

def field_MinimalQuantity(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Visibility(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_AdditionalShippingCost(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Unity(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_UnitPrice(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_ShortDescription(config,product,index):#ekdotis requires product[0] productsMeta[index][0]
    if not config:
        return ";"
    else:
        return productsMeta[index][0] + ";"

def field_Descritpion(config,product,index):#requires product,index,productsMeta (under construction)
    if not config:
        return ";"
    else:
        return "<h2 class=\"book-title\"><strong>{0}</strong></h2></br>\
<span style=\"font-size:14px\"><span style=\"font-family: times new roman,times\"><br/>\
Εκδότης      : <strong><a href=\"http://www.shopbay.gr/el/search?orderby=position&orderway=desc&tag={1}\">{1}</a></strong><br/><br/>\
ISBN         : <strong>{2}</strong><br/><br/>\
Τάξη         : <strong>{3}</strong><br/><br/>\
Σειρα        : <strong><a href=\"http://www.shopbay.gr/el/search?orderby=position&orderway=desc&tag={4}\">{4}</a></strong><br/>\
Συγγραφέας: <strong><a href=\"http://www.shopbay.gr/el/search?orderby=position&orderway=desc&search_query={5}\">{5}</a></strong> <br/><br/>\
Διαθεσιμότητα: Διαθέσιμο<br/></span></span><br/><br/>\
<em><span style=\"color: #808080\">Περιγραφή</span></em><br/>\
<span style=\"font-size:14px\"><span style=\"font-family: times new roman,times\">{6}</span></span>;".format(product[3],
                                                                                                                  productsMeta[index][0],
                                                                                                                  product[6],
                                                                                                                  product[1],
                                                                                                                  product[2],
                                                                                                                  product[5],
                                                                                                                  product[8])

def field_Tags(config,product,index):#Ekdotis Seira Sigkrafeas requires product,index,productsMeta(under construction)
    if not config:
        return ";"
    else:        
        tags = "{0},{1}".format(productsMeta[index][0],product[2].replace("+","plus"))
##        if not product[5]=="":
##            sigrafees = product[5].split(",")
##            for name in sigrafees:
##                tags += ","+name                
        return tags + ";"

def field_MetaTitle(config,product,index):#Titlos Ekdotis Isbn requires product,index,productsMeta(under construction)
    if not config:
        return ";"
    else:
        if (len(product[3])+len(productsMeta[index][0])+len(product[6])) > 67:
            sub = (len(product[3])+len(productsMeta[index][0])+len(product[6])-67)
        else:
            sub = 0
        
        return "{0} {1} {2};".format(product[3][0:len(product[3])-sub].replace('(',' ').replace(')',' '),productsMeta[index][0],product[6])

def field_MetaKeywords(config,product,index):#Ekdotis Seira Sigkrafeas requires product,index,productsMeta(under construction)
    if not config:
        return ";"
    else:
        tags = "{0},{1}".format(productsMeta[index][0],product[2])
        #if not product[5]=="":
         #   sigrafees = product[5].split(",")
          #  for name in sigrafees:
           #     tags += ","+name                
        return tags + ";"

def field_MetaDescription(config,product,index):#titlos ekdotis sigkrafeas requires product,index,productsMeta(under construction)
    if not config:
        return ";"
    else:
        sentence = "Το βιβλίο {0} των εκδόσεων {1}".format(product[3].replace('(',' ').replace(')',' '),productsMeta[index][0])
        if not product[5]=="":            
            sigrafees = product[5].split(",")
            sentence += " συγγραφέας "+sigrafees[0]
            for name in range(1,len(sigrafees)):
                sentence += ","+sigrafees[name]
        if len(sentence)>160:
            sentence = sentence[0:160]
        return sentence + ";"

def field_UrlRewriten(config,product):#url-friendly Titlos requires product[3]
    if not config:
        return ";"
    else:
        url = "".join(map(lambda x: "" if x in ";,?!_.)(][:'$&΄" else x,product[3].replace(" ","-").replace("+","-")))
        if len(url)> 150:
            url = url[0:150]
        return url + ";"

def field_TextWhenInStock(config):#fixed behaviour
    if not config:
        return ";"
    else:
        return "Αποστολή σε 1-3 εργάσιμες ημέρες;"
    
def field_TextWhenBackOrderAllowed(config):#Not active
    if not config:
        return ";"
    else:
        return "undefined-field;"


def field_AvailableForOrder(config,product):#requires product[7]
    if not config:
        return ";"
    else:
        return "0;" if product[7] == "" else "1;"

def field_ProductAvailableDate(config):#requires datetime, settings["getDate"]
    if not config:
        return ";"
    else:        
        return settings["getDate"] + ";"

def field_ProductCreationDate(config):#requires datetime, settings["getDate"]
    if not config:
        return ";"
    else:
        return settings["getDate"] + ";"

def field_ShowPrice(config):#fixed behaviour
    if not config:
        return ";"
    else:
        return "1;"

def field_ImgUrls(config,product):#requires product[9]
    if not config:
        return ";"
    else:
        if product[9].strip() == "":
            return "https://sdvigpress.org/images/publi/_default.jpg;"
        else:
            return product[9] + ";"

def field_DeleteExistingImages(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Feature(config,product):#Etos ekdosis requires product[4]
    if not config:
        return ";"
    else:
        return "Έτος έκδοσης:{0};".format(product[4])

def field_AvailableOnlineOnly(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Condition(config):#fixed behaviour
    if not config:
        return ";"
    else:
        return "new;"

def field_Customizable(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_UploadableFiles(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_TextFields(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_OutOfStock(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_IdNameOfShop(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_AdvancedStockManagement(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_DependsOnStock(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

def field_Warehouse(config):#Not Active
    if not config:
        return ";"
    else:
        return "undefined-field;"

#validation functions
def ekdotesVal(product):#requires product[0],ekdotes
    for lang in ekdotes.keys():
        for ekd in ekdotes[lang].keys():
            if ekd.lower() in product[0].strip().lower():
                return ekd,lang
            else:
                for permutation in ekdotes[lang][ekd]:
                    if permutation in product[0].strip().lower():
                        return ekd,lang
    return product[0].strip(),"unassigned"

#data stuctures
settings = {
    "startID":0,#int n>0
    "isActive":"1",#string
    "tax":1.06,#float
    "taxRule":"14",#string
    "wholesale_sale":0.3,#float
    "onSale":"0",#string
    "shopbay_sale":"20",#string
    "getDate": datetime.datetime.now().strftime("%Y-%m-%d")
    }
#3enoglosa Ekdotes
ekdotes = {
    "english":{
        "Andrew Betsis Elt":["etsis"],
        "Archer Boukouvalas":["oukouval"],
        "Cambridge University Press":["ambridge"],
        "Burlington":["urlington"],
        "Grivas Publications":["rivas"],
        "Express Publishing":["xpress"],
        "Hamilton House":["amilton"],
        "Hillside Press":["illside"],
        "Pearson":["earson","ongman"],
        "Macmillan":["acmillan"],
        "MM Publications":["mm","combo"],
        "National Geographic Cengage Learning":["national geographic","engage","ew editions"],
        "Sylvia Kar Publications":["ylvia"],
        "SuperCourse":["uper"],
        "York Press":[]
        },
    "french":{
        "Trait D'Union":["rait"],
        "Hachette":["achette"],
        "CLE International":["cle"],
        "HATIER":["atier"],
        "Katranidou-Raptakis":["katranid"],
        "Le Livre Ouvert":["ouvert"],
        "Nouvel Esprit":["ouvel","kosvo"],
        "Maison Des Langues":["aison"]
        },
    "german":{
        "Hueber Hellas":["ueber"],
        "Klett Hellas":["lett"],
        "Langenscheidt":["langensch"],
        "Vlachos die Lupe":["lachos"],
        "Praxis":["raxis"]
        },
    "italian":{
        "Edilingua":["dilingua"],
        "Guerra":["uerra"]
        },
    "spanish":{
        "Edelsa":["delsa"],
        "Difusion":["difus"],
        "Edinumen":["dinumen"],
        "S.G.E.L":["sgell"]
        }
    }
#Categories  English
categories = {
    "english":{        
        "2110":["junior a","junior a & b"],
        "2111":["junior b"],
        "2112":["class a","a class","a1","senior a"],
        "2113":["senior b","class b","b class"],
        "2115":["intermediate","senior c","class c","c class","senior d","class d","d class"],
        "2116":["upper intermediate"],
        "2117":["begginer"],
        "2118":["elementary"],
        "2119":["pre-intermediate"],
        "2120":["b1+","senior e"],
        "2121":["c1"],
        "2122":["proficiency","c2","ecpe"],
        "2128":["pre junior"],
        "2130":["dictionary"],
        "2131":["προετοιμασία εξετάσεων","toeic","pte general","ielts"],
        "2132":["first certificate","ecce"],
        "183769":["κρατικό πιστοποιητικό γλωσσομάθειας"],
        "2129":["b2"]        
        }
    }


#Starts here

while True:

    while True:
        readFile = input("Filename to read(example.csv): ")#selects a valid file
        if isfile(readFile):            
            break
        else:
            print("file not found...")
            
    pSizeMax = 0 
    pSizeMin = 99 #won't work with larger sizes
    with open(readFile,"r",encoding ='utf-8') as rawText:#writes file to a list
        productList = []        
        temp = rawText.readline().strip()
        if not("εκδότης;επίπεδο;σειρά;τίτλος;έτος;συγγραφέας;isbn;τιμή;περιγραφή;εικόνα" in temp.lower()):
            productList.append(temp.split(";"))
            if len(productList[-1])> pSizeMax:
                pSizeMax = len(productList[-1])
            if len(productList[-1])< pSizeMin:
                pSizeMin = len(productList[-1])
                
        for i in rawText:
            productList.append(i.strip().split(";"))
            if len(productList[-1])> pSizeMax:
                pSizeMax = len(productList[-1])
            if len(productList[-1])< pSizeMin:
                pSizeMin = len(productList[-1])

    print("Found",len(productList),"products with",len(productList[0]),"fields")#display file info
    if pSizeMax != pSizeMin:
        print("***\nInconsistent number of fields between products... min: {0} max : {1}\n***".format(pSizeMin,pSizeMax))
    print("First entry:\n",productList[0])
    print()
    if(input("Is this the right file?(y/n) ")=="y"):
        break    

productsMeta = []
for index in range(len(productList)):#creates productsMeta
    temp = ekdotesVal(productList[index])
    productsMeta.append([temp[0],temp[1]])
        
while True:
    startIDraw = input("Your starting id number(products will start from 'input + 1'): ").strip()#selects a valid starting id
    if  startIDraw.isnumeric():
        settings["startID"] = int(startIDraw)+1
        break
    else:
        print("Not a valid input...")

reformatedList=[]

for index in range(len(productList)): #len(productList): --> 1 to fill field, 0 to leave it empty  
    reformatedProduct = ""
    reformatedProduct += field_Id(1)    
    reformatedProduct += field_Active(1)
    reformatedProduct += field_Name(1,productList[index])
    reformatedProduct += field_Category(1,productList[index],index)
    reformatedProduct += field_Price(1,productList[index])
    reformatedProduct += field_TaxRule(1)
    reformatedProduct += field_WholeSale(1,productList[index])
    reformatedProduct += field_OnSale(1)
    reformatedProduct += field_DiscountFixed(0)
    reformatedProduct += field_DiscountRelative(1)
    reformatedProduct += field_DiscountFrom(0)
    reformatedProduct += field_DiscountTill(0)
    reformatedProduct += field_Ref_Isbn(1,productList[index])
    reformatedProduct += field_SupplierReference(0)
    reformatedProduct += field_Supplier(0)
    reformatedProduct += field_Manufacturer(1,productList[index],index)
    reformatedProduct += field_Ean13(0)
    reformatedProduct += field_Upc(0)
    reformatedProduct += field_Ecotax(0)
    reformatedProduct += field_Width(0)
    reformatedProduct += field_Height(0)
    reformatedProduct += field_Depth(0)
    reformatedProduct += field_EmptyField(0)
    reformatedProduct += field_Weight(1)
    reformatedProduct += field_EmptyField(0)
    reformatedProduct += field_EmptyField(0)
    reformatedProduct += field_Quantity(1)
    reformatedProduct += field_MinimalQuantity(0)
    reformatedProduct += field_Visibility(0)
    reformatedProduct += field_AdditionalShippingCost(0)
    reformatedProduct += field_Unity(0)
    reformatedProduct += field_UnitPrice(0)
    reformatedProduct += field_EmptyField(0)
    reformatedProduct += field_EmptyField(0)
    reformatedProduct += field_ShortDescription(1,productList[index],index)
    reformatedProduct += field_Descritpion(1,productList[index],index)
    reformatedProduct += field_Tags(1,productList[index],index)
    reformatedProduct += field_MetaTitle(1,productList[index],index)
    reformatedProduct += field_MetaKeywords(1,productList[index],index)
    reformatedProduct += field_MetaDescription(1,productList[index],index)
    reformatedProduct += field_UrlRewriten(1,productList[index])
    reformatedProduct += field_TextWhenInStock(1)
    reformatedProduct += field_TextWhenBackOrderAllowed(0)
    reformatedProduct += field_AvailableForOrder(1,productList[index])
    reformatedProduct += field_ProductAvailableDate(1)
    reformatedProduct += field_ProductCreationDate(1)
    reformatedProduct += field_ShowPrice(1)
    reformatedProduct += field_ImgUrls(1,productList[index])
    reformatedProduct += field_DeleteExistingImages(0)
    reformatedProduct += field_EmptyField(0)
    reformatedProduct += field_Feature(1,productList[index])
    reformatedProduct += field_AvailableOnlineOnly(0)
    reformatedProduct += field_Condition(1)
    reformatedProduct += field_Customizable(0)
    reformatedProduct += field_UploadableFiles(0)
    reformatedProduct += field_TextFields(0)
    reformatedProduct += field_OutOfStock(0)
    reformatedProduct += field_IdNameOfShop(0)
    reformatedProduct += field_AdvancedStockManagement(0)
    reformatedProduct += field_DependsOnStock(0)
    reformatedProduct += field_Warehouse(0)
    reformatedProduct += "\n"
    reformatedList.append(reformatedProduct)

    #print(reformatedProduct)

#check final list
pSizeMax = 0 
pSizeMin = 99 #won't work with larger sizes
for product in productList:
    if len(product)> pSizeMax:
        pSizeMax = len(product) 
    elif len(product)< pSizeMin:
        pSizeMin = len(product)
if pSizeMax != pSizeMin:
    print("***\nInconsistent number of fields between products... min: {0} max : {1}\n***".format(pSizeMin,pSizeMax))

#write to file
while True:
    while True:
        writeFile = input("File to write(example.csv): ")
        if isfile(writeFile):
            if input("file already exists, do you want to overwrite it?[y/n]")=="y":
                break
        else:
            break
    
    try:
        with open(writeFile,"w",encoding ='utf-8') as endfile:
            endfile.write("id;active;title;level;pricenotax;taxrule;wholesale;\
onsale;;discount%;;;isbn;;;ekdotis;;;;;;;;weight;;;\
quantity;;;;;;;;ekdotis;description;tags;meta-title;\
meta-keywords;metadescription;url-rewriten;\
text when in stock;;available for order;;;showprice;img urls;;;\
etos ekdosis;;condition;;;;;;;;\n")
            for line in reformatedList:
                endfile.write(line)
    except:
        print("Something went wrong...")
    else:
        input("Reformat completed with {0}[Enter to quit]".format(writeFile))
        break

