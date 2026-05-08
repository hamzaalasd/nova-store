<?php

namespace App\Filament\Resources\ProductGroups;

use App\Filament\Resources\ProductGroups\Pages\CreateProductGroup;
use App\Filament\Resources\ProductGroups\Pages\EditProductGroup;
use App\Filament\Resources\ProductGroups\Pages\ListProductGroups;
use App\Models\ProductGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Str;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class ProductGroupResource extends Resource
{
    protected static ?string $model = ProductGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';
    public static function getNavigationLabel(): string
    {
        return 'مجموعات المنتجات';
    }

    public static function getModelLabel(): string
    {
        return 'مجموعة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'مجموعات المنتجات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'إدارة المنتجات';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('بيانات المجموعة')
                    ->description('أدخل بيانات مجموعة المنتجات مثل الطاقة الشمسية أو الشاشات أو المستلزمات النسائية.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label('الاسم بالعربية')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                TextInput::make('name_en')
                                    ->label('الاسم بالإنجليزية')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('slug')
                            ->label('الرابط')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Grid::make(2)
                            ->schema([
                                Textarea::make('description_ar')
                                    ->label('الوصف بالعربية')
                                    ->rows(4),

                                Textarea::make('description_en')
                                    ->label('الوصف بالإنجليزية')
                                    ->rows(4),
                            ]),
                    ]),

                Section::make('الصور')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('image')
                                    ->label('صورة المجموعة')
                                    ->image()
                                    ->directory('product-groups')
                                    ->imageEditor(),

                                FileUpload::make('banner_image')
                                    ->label('صورة البنر')
                                    ->image()
                                    ->directory('product-groups/banners')
                                    ->imageEditor(),
                            ]),
                    ]),

                Section::make('الإعدادات')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('مفعلة')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),

                Section::make('تحسين محركات البحث SEO')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('seo_title_ar')
                                    ->label('عنوان SEO بالعربية')
                                    ->maxLength(255),

                                TextInput::make('seo_title_en')
                                    ->label('عنوان SEO بالإنجليزية')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Textarea::make('seo_description_ar')
                                    ->label('وصف SEO بالعربية')
                                    ->rows(3),

                                Textarea::make('seo_description_en')
                                    ->label('وصف SEO بالإنجليزية')
                                    ->rows(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular(),

                TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('الرابط')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->label('تعديل'),
                DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductGroups::route('/'),
            'create' => CreateProductGroup::route('/create'),
            'edit' => EditProductGroup::route('/{record}/edit'),
        ];
    }
}
