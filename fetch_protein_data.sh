#!/bin/bash

# 检查是否提供了蛋白质ID
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <protein_id>"
    exit 1
fi

# 获取蛋白质ID
protein_id=$1

# 设置NCBI的电子邮件地址
email="your-email@example.com"

# 使用esearch查找蛋白质ID
echo "Searching for protein data for ID: $protein_id..."

# 使用efetch获取蛋白质序列
efetch -db protein -id $protein_id -rettype gb -retmode text | grep -A 1 "ORIGIN" | tail -n +2 | tr -d -c 'A-Za-z' > protein_sequence.txt

# 检查结果
if [ -s protein_sequence.txt ]; then
    echo "Protein sequence fetched successfully. The sequence is saved to protein_sequence.txt."
else
    echo "Failed to fetch protein sequence for $protein_id."
    exit 1
fi
